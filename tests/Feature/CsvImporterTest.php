<?php

namespace AestheticStudio\CsvImporter\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use AestheticStudio\CsvImporter\Tests\TestCase;

class CsvImporterTest extends TestCase
{
    /** @test */
    public function it_can_render_the_importer_ui()
    {
        $response = $this->get(route('csv-importer.index'));

        $response->assertStatus(200);
        $response->assertViewIs('csv-importer::importer');
        $response->assertSee('Interactive CSV Importer/Exporter');
        $response->assertSee('contacts'); // Our mock contacts table should be available
    }

    /** @test */
    public function it_fails_uploading_invalid_or_disallowed_tables()
    {
        $file = UploadedFile::fake()->create('test.csv');

        $response = $this->postJson(route('csv-importer.upload'), [
            'table' => 'invalid_table_name',
            'csv_file' => $file,
            'import_type' => 'insert',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'The selected database table is invalid or not allowed.'
        ]);
    }

    /** @test */
    public function it_can_upload_and_parse_csv_successfully()
    {
        $csvContent = "Full Name,E-mail Address,Telephone,Age\n" .
                      "Alice Smith,alice@example.com,98765432,30\n" .
                      "Bob Johnson,bob@example.com,11223344,45";

        $file = UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->postJson(route('csv-importer.upload'), [
            'table' => 'contacts',
            'csv_file' => $file,
            'import_type' => 'insert',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'headers',
            'rows',
            'db_columns',
            'mappings',
            'table'
        ]);

        $responseData = $response->json();
        
        $this->assertTrue($responseData['success']);
        $this->assertEquals(['Full Name', 'E-mail Address', 'Telephone', 'Age'], $responseData['headers']);
        
        // Assert smart matching auto-mapped fields correctly
        $this->assertEquals('name', $responseData['mappings']['Full Name']);
        $this->assertEquals('email', $responseData['mappings']['E-mail Address']);
        $this->assertEquals('phone', $responseData['mappings']['Telephone']);
        $this->assertEquals('age', $responseData['mappings']['Age']);

        // Assert table rows parsed correctly
        $this->assertCount(2, $responseData['rows']);
        $this->assertEquals('Alice Smith', $responseData['rows'][0]['Full Name']);
    }

    /** @test */
    public function it_excludes_id_when_uploading_for_insert()
    {
        $csvContent = "Full Name,E-mail Address\nAlice,alice@example.com";
        $file = UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->postJson(route('csv-importer.upload'), [
            'table' => 'contacts',
            'csv_file' => $file,
            'import_type' => 'insert',
        ]);

        $response->assertStatus(200);
        $dbColumns = $response->json('db_columns');
        
        $this->assertNotContains('id', $dbColumns);
    }

    /** @test */
    public function it_includes_id_when_uploading_for_update()
    {
        $csvContent = "ID,Full Name,E-mail Address\n1,Alice,alice@example.com";
        $file = UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->postJson(route('csv-importer.upload'), [
            'table' => 'contacts',
            'csv_file' => $file,
            'import_type' => 'update',
        ]);

        $response->assertStatus(200);
        $dbColumns = $response->json('db_columns');
        
        $this->assertContains('id', $dbColumns);
    }

    /** @test */
    public function it_can_import_mapped_csv_data_into_the_database()
    {
        $mappings = [
            'Full Name' => 'name',
            'E-mail Address' => 'email',
            'Telephone' => 'phone',
            'Age' => 'age'
        ];

        $data = [
            [
                'Full Name' => 'Charlie Brown',
                'E-mail Address' => 'charlie@example.com',
                'Telephone' => '5551234',
                'Age' => '12'
            ],
            [
                'Full Name' => 'David Miller',
                'E-mail Address' => 'david@example.com',
                'Telephone' => '4449876',
                'Age' => '28'
            ]
        ];

        $response = $this->postJson(route('csv-importer.import'), [
            'table' => 'contacts',
            'mappings' => $mappings,
            'data' => $data,
            'import_type' => 'insert',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Successfully verified and imported 2 records into \'contacts\'.'
        ]);

        // Verify rows are actually in the mock database table!
        $this->assertDatabaseHas('contacts', [
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'phone' => '5551234',
            'age' => 12
        ]);

        $this->assertDatabaseHas('contacts', [
            'name' => 'David Miller',
            'email' => 'david@example.com',
            'phone' => '4449876',
            'age' => 28
        ]);
    }

    /** @test */
    public function it_fails_importing_in_update_mode_if_id_column_not_mapped()
    {
        $mappings = [
            'Full Name' => 'name',
            'E-mail Address' => 'email',
        ];

        $data = [
            [
                'Full Name' => 'Charlie Brown',
                'E-mail Address' => 'charlie@example.com',
            ]
        ];

        $response = $this->postJson(route('csv-importer.import'), [
            'table' => 'contacts',
            'mappings' => $mappings,
            'data' => $data,
            'import_type' => 'update',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'The ID column is required and must be mapped for updating records.'
        ]);
    }

    /** @test */
    public function it_fails_importing_in_update_mode_if_id_value_is_missing_in_row()
    {
        $mappings = [
            'ID' => 'id',
            'Full Name' => 'name',
        ];

        $data = [
            [
                'ID' => '',
                'Full Name' => 'Charlie Brown',
            ]
        ];

        $response = $this->postJson(route('csv-importer.import'), [
            'table' => 'contacts',
            'mappings' => $mappings,
            'data' => $data,
            'import_type' => 'update',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('missing a value for the required ID column', $response->json('message'));
    }

    /** @test */
    public function it_fails_importing_in_update_mode_if_id_does_not_exist_in_db()
    {
        $mappings = [
            'ID' => 'id',
            'Full Name' => 'name',
        ];

        $data = [
            [
                'ID' => '999',
                'Full Name' => 'Charlie Brown',
            ]
        ];

        $response = $this->postJson(route('csv-importer.import'), [
            'table' => 'contacts',
            'mappings' => $mappings,
            'data' => $data,
            'import_type' => 'update',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('does not exist in \'contacts\'', $response->json('message'));
    }

    /** @test */
    public function it_successfully_updates_existing_records_in_update_mode()
    {
        // First insert a record to update
        $contactId = DB::table('contacts')->insertGetId([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '1111111',
            'age' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mappings = [
            'Record ID' => 'id',
            'Full Name' => 'name',
            'E-mail Address' => 'email',
        ];

        $data = [
            [
                'Record ID' => (string) $contactId,
                'Full Name' => 'New Name',
                'E-mail Address' => 'new@example.com',
            ]
        ];

        $response = $this->postJson(route('csv-importer.import'), [
            'table' => 'contacts',
            'mappings' => $mappings,
            'data' => $data,
            'import_type' => 'update',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Successfully verified and updated 1 records in \'contacts\'.'
        ]);

        // Verify the record was actually updated!
        $this->assertDatabaseHas('contacts', [
            'id' => $contactId,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '1111111', // Unchanged field remains the same
            'age' => 20,          // Unchanged field remains the same
        ]);
    }

    /** @test */
    public function it_fails_exporting_invalid_or_disallowed_tables()
    {
        $response = $this->get(route('csv-importer.export', ['table' => 'invalid_table_name']));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_export_table_to_csv_successfully()
    {
        // Insert a record into database first
        $contactId = DB::table('contacts')->insertGetId([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567',
            'age' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('csv-importer.export', ['table' => 'contacts']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=contacts_export_' . date('Y-m-d') . '.csv');

        $output = $response->streamedContent();

        $this->assertStringContainsString('id', $output);
        $this->assertStringContainsString('name', $output);
        $this->assertStringContainsString('email', $output);
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('john@example.com', $output);
    }

    /** @test */
    public function it_fails_previewing_invalid_or_disallowed_tables()
    {
        $response = $this->getJson(route('csv-importer.preview', ['table' => 'invalid_table_name']));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_preview_table_records_successfully()
    {
        // Insert a record into database first
        $contactId = DB::table('contacts')->insertGetId([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '7654321',
            'age' => 22,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson(route('csv-importer.preview', ['table' => 'contacts']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'columns',
            'rows',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertContains('name', $response->json('columns'));
        $this->assertContains('email', $response->json('columns'));
        $this->assertCount(1, $response->json('rows'));
        $this->assertEquals('Jane Doe', $response->json('rows')[0]['name']);
    }
}
