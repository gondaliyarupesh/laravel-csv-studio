<script>
document.addEventListener('DOMContentLoaded', function () {
    // State management
    let state = {
        activeMode: 'welcome', // welcome, import, export
        currentStep: 1,
        selectedTable: '',
        importType: 'insert',
        csvFile: null,
        csvHeaders: [],
        csvRows: [],
        dbColumns: [],
        mappings: {}, // csvHeader -> dbColumn
        errors: {} // rowIndex -> { csvHeader -> errorMsg }
    };

    // DOM Elements
    const welcomeScreen = document.getElementById('welcome_screen');
    const exportScreen = document.getElementById('export_screen');
    const cardModeImport = document.getElementById('card_mode_import');
    const cardModeExport = document.getElementById('card_mode_export');
    const btnImportCancel = document.getElementById('btn_import_cancel');
    const btnExportBack = document.getElementById('btn_export_back');
    const btnExportSubmit = document.getElementById('btn_export_submit');
    const exportTableSelect = document.getElementById('export_table_select');
    const exportPreviewTable = document.getElementById('export_preview_table');
    const exportPreviewHead = document.getElementById('export_preview_head');
    const exportPreviewBody = document.getElementById('export_preview_body');
    const exportEmptyState = document.getElementById('export_empty_state');
    const exportRowCountBadge = document.getElementById('export_row_count_badge');
    const exportPreviewContainer = document.getElementById('export_preview_container');

    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('csv_file');
    const fileInfo = document.getElementById('file_info');
    const fileNameSpan = document.getElementById('file_name');
    const fileRemoveBtn = document.getElementById('file_remove');
    const tableSelect = document.getElementById('target_table');
    const btnNext = document.getElementById('btn_next');
    const loaderOverlay = document.getElementById('loader_overlay');
    const loaderText = document.getElementById('loader_text');
    const successModal = document.getElementById('success_modal');
    const successMessage = document.getElementById('success_message');
    const btnDone = document.getElementById('btn_done');

    const stepScreens = {
        1: document.getElementById('step_1_screen'),
        2: document.getElementById('step_2_screen')
    };

    const stepsIndicators = {
        1: document.getElementById('step_1_indicator'),
        2: document.getElementById('step_2_indicator')
    };

    const stepDividers = {
        1: document.getElementById('step_1_divider')
    };

    const stepperContainer = document.querySelector('.stepper');

    // --- MODE SELECTOR & TRANSITION LOGIC ---

    cardModeImport.addEventListener('click', () => {
        state.activeMode = 'import';
        welcomeScreen.classList.remove('active');
        stepperContainer.style.display = 'flex';
        stepScreens[1].classList.add('active');
        goToStep(1);
    });

    cardModeExport.addEventListener('click', () => {
        state.activeMode = 'export';
        welcomeScreen.classList.remove('active');
        stepperContainer.style.display = 'none';
        exportScreen.classList.add('active');
        resetExportScreen();
    });

    btnImportCancel.addEventListener('click', () => {
        // Clear file upload state
        state.csvFile = null;
        fileInput.value = '';
        fileInfo.style.display = 'none';
        dropzone.style.display = 'flex';
        tableSelect.selectedIndex = 0;
        document.getElementById('table_actions_container').style.display = 'none';
        validateStep1();

        state.activeMode = 'welcome';
        stepScreens[1].classList.remove('active');
        stepperContainer.style.display = 'none';
        welcomeScreen.classList.add('active');
    });

    btnExportBack.addEventListener('click', () => {
        state.activeMode = 'welcome';
        exportScreen.classList.remove('active');
        welcomeScreen.classList.add('active');
        resetExportScreen();
    });

    function resetExportScreen() {
        exportTableSelect.selectedIndex = 0;
        exportEmptyState.style.display = 'flex';
        exportEmptyState.innerHTML = `
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 0.75rem; opacity: 0.5;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            <p>Select a table above to view live database records.</p>
        `;
        exportPreviewTable.style.display = 'none';
        exportRowCountBadge.textContent = 'No table selected';
        btnExportSubmit.setAttribute('disabled', 'true');
    }

    // --- TABLE EXPORT RECORD PREVIEW & ACTION ---

    exportTableSelect.addEventListener('change', (e) => {
        const tableName = e.target.value;
        if (!tableName) return;

        // Show inline loading state in empty state
        exportPreviewTable.style.display = 'none';
        exportEmptyState.style.display = 'flex';
        exportEmptyState.innerHTML = `
            <div class="spinner" style="width: 40px; height: 40px; margin-bottom: 0.75rem;"></div>
            <p>Querying database for '${tableName}' records...</p>
        `;
        exportRowCountBadge.textContent = 'Loading...';
        btnExportSubmit.setAttribute('disabled', 'true');

        // Dynamically compute preview route using index route and replacing back/append
        const baseUrl = window.location.pathname.replace(/\/$/, "");
        
        fetch(`${baseUrl}/preview/${tableName}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderExportPreviewTable(data.columns, data.rows);
                    // Update Export button to point to the actual streamed export endpoint
                    btnExportSubmit.removeAttribute('disabled');
                    btnExportSubmit.onclick = () => {
                        window.location.href = `${baseUrl}/export/${tableName}`;
                    };
                } else {
                    exportEmptyState.innerHTML = `
                        <p style="color: var(--error); font-weight: bold;">Error loading preview:</p>
                        <p style="color: var(--text-secondary); margin-top: 0.25rem;">${data.message}</p>
                    `;
                    exportRowCountBadge.textContent = 'Failed to load preview';
                }
            })
            .catch(error => {
                console.error(error);
                exportEmptyState.innerHTML = `
                    <p style="color: var(--error); font-weight: bold;">Unexpected network or database error.</p>
                `;
                exportRowCountBadge.textContent = 'Connection error';
            });
    });

    function renderExportPreviewTable(columns, rows) {
        exportPreviewHead.innerHTML = '';
        exportPreviewBody.innerHTML = '';

        if (columns.length === 0) {
            exportEmptyState.style.display = 'flex';
            exportEmptyState.innerHTML = '<p>No columns found in this database table.</p>';
            exportPreviewTable.style.display = 'none';
            exportRowCountBadge.textContent = '0 Columns';
            return;
        }

        // Render Head
        const thr = document.createElement('tr');
        const thIndex = document.createElement('th');
        thIndex.textContent = '#';
        thIndex.style.width = '50px';
        thr.appendChild(thIndex);

        columns.forEach(col => {
            const th = document.createElement('th');
            th.textContent = col;
            thr.appendChild(th);
        });
        exportPreviewHead.appendChild(thr);

        // Render Body
        if (rows.length === 0) {
            exportEmptyState.style.display = 'flex';
            exportEmptyState.innerHTML = `
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 0.75rem; opacity: 0.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <p>The selected database table is currently empty.</p>
            `;
            exportPreviewTable.style.display = 'none';
            exportRowCountBadge.textContent = '0 Records';
            return;
        }

        rows.forEach((row, rIndex) => {
            const tr = document.createElement('tr');
            
            const tdIndex = document.createElement('td');
            tdIndex.textContent = rIndex + 1;
            tdIndex.style.color = 'var(--text-secondary)';
            tr.appendChild(tdIndex);

            columns.forEach(col => {
                const td = document.createElement('td');
                td.textContent = row[col] !== null && row[col] !== undefined ? row[col] : '';
                tr.appendChild(td);
            });

            exportPreviewBody.appendChild(tr);
        });

        // Show table, hide empty state
        exportEmptyState.style.display = 'none';
        exportPreviewTable.style.display = 'table';
        exportRowCountBadge.textContent = `Showing up to ${rows.length} record(s)`;
    }

    // --- STEP 1: UPLOAD & SELECTION ---

    // Drag and Drop Events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            handleFileSelect(files[0]);
        }
    });

    dropzone.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            alert('Please select a valid CSV file.');
            return;
        }
        state.csvFile = file;
        fileNameSpan.textContent = `${file.name} (${formatBytes(file.size)})`;
        fileInfo.style.display = 'flex';
        dropzone.style.display = 'none';
        validateStep1();
    }

    fileRemoveBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        state.csvFile = null;
        fileInput.value = '';
        fileInfo.style.display = 'none';
        dropzone.style.display = 'flex';
        validateStep1();
    });

    tableSelect.addEventListener('change', (e) => {
        state.selectedTable = e.target.value;
        
        const exportContainer = document.getElementById('table_actions_container');
        const exportBtn = document.getElementById('btn_export_table');
        if (state.selectedTable) {
            exportContainer.style.display = 'flex';
            // Generate the dynamic Laravel route URL manually using window.location path
            const baseUrl = window.location.pathname.replace(/\/$/, "");
            exportBtn.href = `${baseUrl}/export/${state.selectedTable}`;
        } else {
            exportContainer.style.display = 'none';
        }
        
        validateStep1();
    });

    // Import Mode selection tiles
    const modeOptions = document.querySelectorAll('.mode-option');
    modeOptions.forEach(option => {
        option.addEventListener('click', function () {
            modeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            state.importType = radio.value;
        });
    });

    function validateStep1() {
        if (state.csvFile && state.selectedTable) {
            btnNext.removeAttribute('disabled');
        } else {
            btnNext.setAttribute('disabled', 'true');
        }
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // --- NAVIGATION: STEP 1 TO STEP 2 ---

    btnNext.addEventListener('click', () => {
        if (!state.csvFile || !state.selectedTable) return;

        showLoader('Uploading and analyzing CSV file...');

        const formData = new FormData();
        formData.append('csv_file', state.csvFile);
        formData.append('table', state.selectedTable);
        formData.append('import_type', state.importType);

        fetch("{{ route('csv-importer.upload') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoader();
            if (data.success) {
                state.csvHeaders = data.headers;
                state.csvRows = data.rows;
                state.dbColumns = data.db_columns;
                state.mappings = data.mappings;
                
                goToStep(2);
                renderMappingSection();
                renderInteractiveTable();
            } else {
                alert(data.message || 'Error parsing the file.');
            }
        })
        .catch(error => {
            hideLoader();
            console.error('Error:', error);
            alert('An unexpected error occurred. Please check the console.');
        });
    });

    function goToStep(step) {
        // Update screens
        Object.keys(stepScreens).forEach(s => {
            if (parseInt(s) === step) {
                stepScreens[s].classList.add('active');
            } else {
                stepScreens[s].classList.remove('active');
            }
        });

        // Update indicators
        if (step === 2) {
            stepsIndicators[1].classList.remove('active');
            stepsIndicators[1].classList.add('completed');
            stepsIndicators[2].classList.add('active');
            stepDividers[1].classList.add('active');
        } else {
            stepsIndicators[1].classList.add('active');
            stepsIndicators[1].classList.remove('completed');
            stepsIndicators[2].classList.remove('active');
            stepDividers[1].classList.remove('active');
        }
        
        state.currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // --- STEP 2: MAPPING & INTERACTIVE GRID ---

    function renderMappingSection() {
        const grid = document.getElementById('mapping_grid');
        grid.innerHTML = '';

        state.csvHeaders.forEach(header => {
            const card = document.createElement('div');
            card.className = 'mapping-card';
            if (state.mappings[header]) {
                card.classList.add('mapped');
            }

            const headerLabel = document.createElement('div');
            headerLabel.className = 'mapping-header-name';
            headerLabel.textContent = `CSV: ${header}`;

            const select = document.createElement('select');
            select.dataset.header = header;
            
            // Add Empty option for unmapped columns
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Do Not Import';
            select.appendChild(defaultOpt);

            // Add Table Database columns
            state.dbColumns.forEach(col => {
                const opt = document.createElement('option');
                opt.value = col;
                opt.textContent = col;
                if (state.mappings[header] === col) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });

            select.addEventListener('change', (e) => {
                const val = e.target.value;
                state.mappings[header] = val;
                
                if (val) {
                    card.classList.add('mapped');
                } else {
                    card.classList.remove('mapped');
                }

                // Re-run validation for all rows on this specific column
                validateDataset();
                renderInteractiveTable();
            });

            card.appendChild(headerLabel);
            card.appendChild(select);
            grid.appendChild(card);
        });
    }

    function renderInteractiveTable() {
        const thead = document.getElementById('table_head');
        const tbody = document.getElementById('table_body');
        
        thead.innerHTML = '';
        tbody.innerHTML = '';

        // Render Table Headers
        const headerRow = document.createElement('tr');
        
        // Row index header
        const indexTh = document.createElement('th');
        indexTh.textContent = '#';
        indexTh.style.width = '50px';
        headerRow.appendChild(indexTh);

        // CSV column headers with their mappings highlighted
        state.csvHeaders.forEach(header => {
            const th = document.createElement('th');
            
            const headerTitle = document.createElement('div');
            headerTitle.textContent = header;
            th.appendChild(headerTitle);

            const mappingBadge = document.createElement('div');
            mappingBadge.style.fontSize = '0.75rem';
            mappingBadge.style.fontWeight = '500';
            
            if (state.mappings[header]) {
                mappingBadge.style.color = 'var(--accent)';
                mappingBadge.textContent = `➜ ${state.mappings[header]}`;
            } else {
                mappingBadge.style.color = 'var(--text-secondary)';
                mappingBadge.textContent = '➜ Ignore';
            }
            
            th.appendChild(mappingBadge);
            headerRow.appendChild(th);
        });

        // Action header
        const actionTh = document.createElement('th');
        actionTh.textContent = 'Actions';
        actionTh.style.width = '80px';
        headerRow.appendChild(actionTh);

        thead.appendChild(headerRow);

        // Update row stats
        document.getElementById('total_rows_badge').textContent = state.csvRows.length;

        // Run validation first
        validateDataset();

        // Render Rows
        state.csvRows.forEach((row, rowIndex) => {
            const tr = document.createElement('tr');
            
            // Row number
            const indexTd = document.createElement('td');
            indexTd.textContent = rowIndex + 1;
            indexTd.style.color = 'var(--text-secondary)';
            tr.appendChild(indexTd);

            // Cells for each CSV column
            state.csvHeaders.forEach(header => {
                const td = document.createElement('td');
                td.className = 'editable-cell';
                td.textContent = row[header] !== null ? row[header] : '';

                // Check for errors on this cell
                if (state.errors[rowIndex] && state.errors[rowIndex][header]) {
                    td.classList.add('cell-error');
                    td.setAttribute('data-tooltip', state.errors[rowIndex][header]);
                }

                // Inline Edit Events
                td.addEventListener('dblclick', () => {
                    startInlineEdit(td, rowIndex, header);
                });

                // Mobile or single click helper
                td.addEventListener('click', (e) => {
                    // Double click is native, but let's allow single click if focused or trigger with edit indicator
                    if (e.detail === 2) return; 
                });

                tr.appendChild(td);
            });

            // Action Cell (Delete button)
            const actionTd = document.createElement('td');
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'row-delete-btn';
            deleteBtn.innerHTML = `
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            `;
            deleteBtn.addEventListener('click', () => {
                deleteRow(rowIndex);
            });
            actionTd.appendChild(deleteBtn);
            tr.appendChild(actionTd);

            tbody.appendChild(tr);
        });
    }

    function startInlineEdit(td, rowIndex, header) {
        if (td.querySelector('input')) return; // Already editing

        const currentValue = td.textContent;
        td.textContent = '';
        
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'cell-input';
        input.value = currentValue;
        
        // Remove error formatting while editing to avoid visual clutter
        td.classList.remove('cell-error');
        td.removeAttribute('data-tooltip');

        function saveValue() {
            const newValue = input.value.trim();
            state.csvRows[rowIndex][header] = newValue;
            td.textContent = newValue;
            
            // Re-validate just this row/cell and render table updates
            validateDataset();
            renderInteractiveTable();
        }

        input.addEventListener('blur', saveValue);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                input.blur();
            } else if (e.key === 'Escape') {
                td.textContent = currentValue; // Cancel edit
                renderInteractiveTable();
            }
        });

        td.appendChild(input);
        input.focus();
        input.select();
    }

    // --- DATASET VALIDATION (FLEXIBLE CLIENT SIDE RULES) ---

    function validateDataset() {
        state.errors = {};
        let totalErrors = 0;

        // Dynamic validation regex based on configuration columns
        const rules = {
            'email': /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            'phone': /^\+?[0-9\s\-()]{7,20}$/,
            'age': /^[0-9]+$/,
            'number': /^[0-9]+(\.[0-9]+)?$/,
        };

        // Find mapped ID header if in update mode
        let idHeader = null;
        if (state.importType === 'update') {
            idHeader = Object.keys(state.mappings).find(key => state.mappings[key] === 'id');
        }

        state.csvRows.forEach((row, rowIndex) => {
            // Validate required ID if in update mode and column is mapped
            if (state.importType === 'update' && idHeader) {
                const idVal = (row[idHeader] || '').toString().trim();
                if (idVal === '') {
                    addError(rowIndex, idHeader, 'ID is required for update mode');
                    totalErrors++;
                }
            }

            state.csvHeaders.forEach(header => {
                const dbCol = state.mappings[header];
                if (!dbCol) return; // Skip ignored columns

                const val = (row[header] || '').toString().trim();

                // 1. Basic Required check: if DB column name looks like a required field
                // In production database schemas we usually match column names like 'email', 'name', 'title' as essential
                if (['name', 'email', 'title', 'subject'].includes(dbCol) && val === '') {
                    addError(rowIndex, header, `${dbCol} is a required field`);
                    totalErrors++;
                }

                // 2. Format checks
                if (val !== '') {
                    if (dbCol.includes('email') && !rules.email.test(val)) {
                        addError(rowIndex, header, 'Invalid email format (e.g. user@domain.com)');
                        totalErrors++;
                    } else if ((dbCol.includes('phone') || dbCol.includes('mobile')) && !rules.phone.test(val)) {
                        addError(rowIndex, header, 'Invalid phone number format');
                        totalErrors++;
                    } else if (dbCol.includes('age') && !rules.age.test(val)) {
                        addError(rowIndex, header, 'Age must be a positive integer');
                        totalErrors++;
                    } else if ((dbCol.includes('price') || dbCol.includes('amount')) && !rules.number.test(val)) {
                        addError(rowIndex, header, 'Must be a valid numeric amount');
                        totalErrors++;
                    }
                }
            });
        });

        // Update error badge
        const errorBadge = document.getElementById('error_count_badge');
        errorBadge.textContent = totalErrors;
        if (totalErrors > 0) {
            errorBadge.style.background = 'var(--error)';
            errorBadge.style.color = '#fff';
        } else {
            errorBadge.style.background = 'rgba(255, 255, 255, 0.08)';
            errorBadge.style.color = 'var(--text-secondary)';
        }
    }

    function addError(rowIndex, header, message) {
        if (!state.errors[rowIndex]) {
            state.errors[rowIndex] = {};
        }
        state.errors[rowIndex][header] = message;
    }

    // --- ROW ADD & DELETE ---

    document.getElementById('btn_add_row').addEventListener('click', () => {
        const newRow = {};
        state.csvHeaders.forEach(header => {
            newRow[header] = '';
        });
        state.csvRows.push(newRow);
        
        renderInteractiveTable();

        // Automatically trigger inline-edit on the first cell of the newly added row
        setTimeout(() => {
            const tbody = document.getElementById('table_body');
            const lastRow = tbody.lastElementChild;
            if (lastRow) {
                const firstEditableCell = lastRow.querySelector('.editable-cell');
                if (firstEditableCell) {
                    firstEditableCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Double click trigger
                    var event = new MouseEvent('dblclick', {
                        'view': window,
                        'bubbles': true,
                        'cancelable': true
                    });
                    firstEditableCell.dispatchEvent(event);
                }
            }
        }, 100);
    });

    function deleteRow(rowIndex) {
        state.csvRows.splice(rowIndex, 1);
        renderInteractiveTable();
    }

    // --- EXPORT EDITED GRID DATA TO CSV ---

    document.getElementById('btn_export_grid').addEventListener('click', () => {
        if (!state.csvRows || state.csvRows.length === 0) {
            alert('No data available to export.');
            return;
        }

        // Generate CSV content
        let csvContent = "";
        
        // Headers
        csvContent += state.csvHeaders.map(header => `"${header.replace(/"/g, '""')}"`).join(",") + "\n";
        
        // Rows
        state.csvRows.forEach(row => {
            let rowContent = state.csvHeaders.map(header => {
                let cellVal = row[header] !== null ? row[header] : '';
                return `"${cellVal.toString().replace(/"/g, '""')}"`;
            }).join(",");
            csvContent += rowContent + "\n";
        });

        // Trigger browser download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `edited_${state.selectedTable}_${new Date().toISOString().slice(0, 10)}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // --- NAVIGATION: STEP 2 BACK TO STEP 1 ---

    document.getElementById('btn_back').addEventListener('click', () => {
        goToStep(1);
    });

    // --- STEP 3: VERIFY & DB IMPORT SUBMISSION ---

    document.getElementById('btn_import').addEventListener('click', () => {
        // First re-validate
        validateDataset();
        
        // Count total errors
        let errorCount = 0;
        Object.keys(state.errors).forEach(key => {
            errorCount += Object.keys(state.errors[key]).length;
        });

        if (errorCount > 0) {
            alert(`Please resolve the ${errorCount} highlighted validation error(s) before importing.`);
            
            // Scroll to first error cell
            const firstErrorCell = document.querySelector('.cell-error');
            if (firstErrorCell) {
                firstErrorCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Check if at least one column is mapped
        const mappedColumns = Object.values(state.mappings).filter(v => v !== '');
        if (mappedColumns.length === 0) {
            alert('Please map at least one CSV column to a database column before importing.');
            return;
        }

        // Under Update mode, ensure ID column is mapped
        if (state.importType === 'update') {
            const isIdMapped = Object.values(state.mappings).includes('id');
            if (!isIdMapped) {
                alert('The ID column is required and must be mapped to a CSV column in Update mode.');
                return;
            }
        }

        const loaderMsg = state.importType === 'update' 
            ? 'Executing database updates inside transaction...' 
            : 'Executing database inserts inside transaction...';

        showLoader(loaderMsg);

        fetch("{{ route('csv-importer.import') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                table: state.selectedTable,
                import_type: state.importType,
                mappings: state.mappings,
                data: state.csvRows
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoader();
            if (data.success) {
                successMessage.textContent = data.message;
                successModal.style.display = 'flex';
            } else {
                alert(data.message || 'Database error occurred.');
            }
        })
        .catch(error => {
            hideLoader();
            console.error('Error:', error);
            alert('An unexpected network or server error occurred. Please verify your table structure.');
        });
    });

    // Reset workflow when completed
    btnDone.addEventListener('click', () => {
        successModal.style.display = 'none';
        
        // Reset state
        state.csvFile = null;
        state.selectedTable = '';
        state.importType = 'insert';
        state.csvHeaders = [];
        state.csvRows = [];
        state.dbColumns = [];
        state.mappings = {};
        state.errors = {};

        // Reset import mode options in UI
        const modeOptions = document.querySelectorAll('.mode-option');
        modeOptions.forEach(opt => {
            if (opt.querySelector('input[value="insert"]')) {
                opt.classList.add('active');
                opt.querySelector('input').checked = true;
            } else {
                opt.classList.remove('active');
            }
        });

        fileInput.value = '';
        fileInfo.style.display = 'none';
        dropzone.style.display = 'flex';
        tableSelect.selectedIndex = 0;
        document.getElementById('table_actions_container').style.display = 'none';
        
        state.activeMode = 'welcome';
        stepScreens[1].classList.remove('active');
        stepScreens[2].classList.remove('active');
        stepperContainer.style.display = 'none';
        welcomeScreen.classList.add('active');
        validateStep1();
    });

    // --- LOADER OVERLAY FUNCTIONS ---

    function showLoader(message) {
        loaderText.textContent = message;
        loaderOverlay.style.display = 'flex';
    }

    function hideLoader() {
        loaderOverlay.style.display = 'none';
    }
});
</script>
