@extends('csv-importer::layout')

@section('title', 'Interactive CSV Importer/Exporter')

@section('content')
    <!-- Elegant Header -->
    <header>
        <h1>Interactive CSV Importer/Exporter</h1>
        <p>Upload, map, inline-edit, and verify CSV data, or browse and export clean table backups dynamically.</p>
    </header>

    <!-- Multi-Step Progress Tracker -->
    <div class="stepper" style="display: none;">
        <div id="step_1_indicator" class="step active">
            <span class="step-num">1</span>
            <span>Upload & Table Selection</span>
        </div>
        <div id="step_1_divider" class="step-divider"></div>
        <div id="step_2_indicator" class="step">
            <span class="step-num">2</span>
            <span>Map Columns & Verify Data</span>
        </div>
    </div>

    <!-- Main Dynamic Glassmorphism Card -->
    <div class="glass-card">
        
        <!-- STEP 0: WELCOME SCREEN -->
        <div id="welcome_screen" class="step-screen active">
            <div class="welcome-container">
                <h2 style="font-family: var(--font-heading); font-size: 1.8rem; margin-bottom: 0.5rem; text-align: center; color: var(--text-primary);">Choose Your Workflow</h2>
                <p style="color: var(--text-secondary); text-align: center; margin-bottom: 2.25rem; font-size: 0.95rem;">Select whether you want to import a CSV file or export an existing database table.</p>
                
                <div class="mode-cards-grid">
                    <!-- Import Card -->
                    <div class="welcome-card" id="card_mode_import">
                        <div class="welcome-card-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--primary);">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
                        <h3 class="welcome-card-title">Import CSV Data</h3>
                        <p class="welcome-card-desc">Upload a CSV file, map columns dynamically, perform inline data edits/validation, and import into your database.</p>
                        <div class="btn btn-primary" style="margin-top: auto; width: 100%; pointer-events: none; border-radius: 0.5rem; padding: 0.6rem 1.25rem; font-size: 0.9rem;">
                            <span>Launch Importer</span>
                        </div>
                    </div>
                    
                    <!-- Export Card -->
                    <div class="welcome-card" id="card_mode_export">
                        <div class="welcome-card-icon" style="background: rgba(6, 182, 212, 0.1); color: var(--accent);">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </div>
                        <h3 class="welcome-card-title">Export Table Data</h3>
                        <p class="welcome-card-desc">Browse database tables, preview live records dynamically in a premium data grid, and export clean, verified CSV backups.</p>
                        <div class="btn btn-secondary" style="margin-top: auto; width: 100%; pointer-events: none; border-radius: 0.5rem; padding: 0.6rem 1.25rem; font-size: 0.9rem; background: rgba(6, 182, 212, 0.1); color: var(--accent); border-color: rgba(6, 182, 212, 0.3);">
                            <span>Launch Exporter</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- STEP 1 SCREEN -->
        <div id="step_1_screen" class="step-screen">
            <div class="upload-panel">
                <!-- Database Destination Selection -->
                <div class="form-group">
                    <label for="target_table">Destination Database Table</label>
                    <div class="select-wrapper">
                        <select id="target_table" name="table">
                            <option value="" disabled selected>-- Select a target database table --</option>
                            @foreach($tables as $table)
                                <option value="{{ $table }}">{{ $table }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Export Database Table Action (Dynamically shown) -->
                <div class="form-group" id="table_actions_container" style="display: none; margin-top: -0.5rem; flex-direction: row; gap: 1rem;">
                    <a id="btn_export_table" href="#" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem; width: 100%; border-radius: 0.75rem; text-decoration: none;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Export Current Table to CSV</span>
                    </a>
                </div>

                <!-- Import Mode Selection -->
                <div class="form-group">
                    <label>Import Mode</label>
                    <div class="import-mode-options">
                        <label class="mode-option active" id="mode_insert_label">
                            <input type="radio" name="import_type" value="insert" checked style="display: none;">
                            <div class="mode-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <div class="mode-details">
                                <span class="mode-title">Insert New Records</span>
                                <span class="mode-desc">Add new entries. Auto-generated IDs will be used; the 'id' column is ignored.</span>
                            </div>
                        </label>
                        <label class="mode-option" id="mode_update_label">
                            <input type="radio" name="import_type" value="update" style="display: none;">
                            <div class="mode-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </div>
                            <div class="mode-details">
                                <span class="mode-title">Update Existing Records</span>
                                <span class="mode-desc">Modify existing entries in place. Mapping a unique 'id' column is required.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Custom Drag & Drop Zone -->
                <div class="form-group">
                    <label>Upload CSV File</label>
                    <div id="dropzone" class="dropzone">
                        <!-- Cloud Upload SVG Icon -->
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <span>Drag & drop your CSV file here, or <strong>browse files</strong></span>
                        <p class="dropzone-sub">Supports .csv or .txt (comma or semicolon separated)</p>
                    </div>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" style="display: none;">
                    
                    <!-- Uploaded File Visual Feedback Card -->
                    <div id="file_info" class="file-info">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span id="file_name" class="file-name">filename.csv (0 KB)</span>
                        <div id="file_remove" class="file-remove" title="Remove file">&times;</div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="panel-footer">
                    <button id="btn_import_cancel" class="btn btn-secondary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span>Back to Menu</span>
                    </button>
                    
                    <button id="btn_next" class="btn btn-primary" disabled>
                        <span>Analyze & Map Columns</span>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 2 SCREEN -->
        <div id="step_2_screen" class="step-screen">
            <div class="editor-panel">
                
                <!-- Dynamic Header Mapping Card -->
                <div class="mapping-grid-container">
                    <div class="mapping-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                        </svg>
                        <span>Map CSV Columns to Database Fields</span>
                    </div>
                    <div id="mapping_grid" class="mapping-grid">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- Interactive Grid Controls -->
                <div class="table-actions">
                    <div class="table-stats">
                        <span id="total_rows_badge" style="background: rgba(139, 92, 246, 0.1); padding: 0.25rem 0.6rem; border-radius: 9999px; margin-right: 0.5rem; font-weight: bold;">0</span> Rows Loaded | 
                        <span id="error_count_badge" style="margin-left: 0.5rem; padding: 0.25rem 0.6rem; border-radius: 9999px; font-weight: bold; background: rgba(255, 255, 255, 0.08); color: var(--text-secondary);">0</span> Validation Errors
                    </div>
                    
                    <div style="display: flex; gap: 0.75rem;">
                        <button id="btn_export_grid" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>Export Grid</span>
                        </button>
                        
                        <button id="btn_add_row" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Add Row</span>
                        </button>
                    </div>
                </div>

                <!-- Main Interactive Table Grid -->
                <div class="table-container">
                    <table>
                        <thead id="table_head">
                            <!-- Populated dynamically via JS -->
                        </thead>
                        <tbody id="table_body">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="form-group" style="margin-top: 0.5rem;">
                    <p style="font-size: 0.8rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.35rem;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <span>Tip: Double-click any cell in the table grid to edit its content inline. Hover over invalid cells to see error tooltips.</span>
                    </p>
                </div>

                <!-- Footer Nav Action Buttons -->
                <div class="panel-footer">
                    <button id="btn_back" class="btn btn-secondary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span>Change File / Table</span>
                    </button>
                    
                    <button id="btn_import" class="btn btn-primary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Verify & Import Data</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- EXPORT SCREEN -->
        <div id="export_screen" class="step-screen">
            <div class="editor-panel">
                <div class="welcome-container" style="text-align: left; max-width: 100%;">
                    <h2 style="font-family: var(--font-heading); font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--text-primary);">Export Database Table</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">Select a database table from the list below to preview its records and export it to a CSV file.</p>
                </div>
                
                <!-- Table Selection -->
                <div class="form-group">
                    <label for="export_table_select">Target Database Table</label>
                    <div class="select-wrapper">
                        <select id="export_table_select" name="export_table">
                            <option value="" disabled selected>-- Select a database table to preview --</option>
                            @foreach($tables as $table)
                                <option value="{{ $table }}">{{ $table }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Live Preview Data Grid Container -->
                <div class="mapping-grid-container" style="margin-top: 1rem;">
                    <div class="mapping-title" style="color: var(--accent); display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span>Live Database Record Preview</span>
                        </div>
                        <span id="export_row_count_badge" class="table-stats" style="font-size: 0.85rem; font-weight: normal; color: var(--text-secondary);">No table selected</span>
                    </div>

                    <!-- Inner Table Container -->
                    <div id="export_preview_container" class="table-container" style="margin-top: 1rem; min-height: 200px; display: flex; align-items: center; justify-content: center; background: rgba(8, 12, 20, 0.4);">
                        <div id="export_empty_state" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 0.75rem; opacity: 0.5;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <p>Select a table above to view live database records.</p>
                        </div>
                        <table id="export_preview_table" style="display: none;">
                            <thead id="export_preview_head"></thead>
                            <tbody id="export_preview_body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Nav Action Buttons -->
                <div class="panel-footer" style="margin-top: 2rem;">
                    <button id="btn_export_back" class="btn btn-secondary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span>Back to Main Menu</span>
                    </button>
                    
                    <button id="btn_export_submit" class="btn btn-primary" disabled>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Export Data to CSV</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- FULL SCREEN DYNAMIC SPINNER LOADER -->
    <div id="loader_overlay" class="loader-overlay">
        <div class="spinner"></div>
        <div id="loader_text" class="loader-text">Loading...</div>
    </div>

    <!-- SUCCESS TRANSITION MODAL -->
    <div id="success_modal" class="modal-overlay">
        <div class="modal-content">
            <div class="success-icon-wrapper">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.746 3.746 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
            </div>
            <h3 class="modal-title">Import Completed!</h3>
            <p id="success_message" class="modal-description">Successfully verified and imported records.</p>
            <button id="btn_done" class="btn btn-primary" style="width: 100%;">
                <span>Done & Reset</span>
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    @include('csv-importer::partials.scripts')
@endsection
