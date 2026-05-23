<style>
/* CSS Reset & Variables */
:root {
    --bg-color: #080c14;
    --card-bg: rgba(17, 24, 39, 0.7);
    --card-border: rgba(255, 255, 255, 0.08);
    --text-primary: #f3f4f6;
    --text-secondary: #9ca3af;
    --primary: #8b5cf6;
    --primary-glow: rgba(139, 92, 246, 0.4);
    --primary-gradient: linear-gradient(135deg, #8b5cf6 0%, #d946ef 100%);
    --accent: #06b6d4;
    --success: #10b981;
    --success-glow: rgba(16, 185, 129, 0.2);
    --error: #ef4444;
    --error-glow: rgba(239, 68, 68, 0.15);
    --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
    --font-heading: 'Outfit', var(--font-sans);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: var(--bg-color);
    background-image: 
        radial-gradient(at 10% 20%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
        radial-gradient(at 90% 80%, rgba(6, 182, 212, 0.1) 0px, transparent 50%);
    font-family: var(--font-sans);
    color: var(--text-primary);
    min-height: 100vh;
    padding: 2.5rem 1rem;
    overflow-x: hidden;
    line-height: 1.5;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

/* Header Aesthetics */
header {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInDown 0.6s ease-out;
}

h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
    letter-spacing: -0.025em;
}

header p {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

/* Glassmorphism Card Wrapper */
.glass-card {
    background: var(--card-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--card-border);
    border-radius: 1.25rem;
    padding: 2.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

.glass-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--primary-gradient);
}

/* Multi-Step Indicator */
.stepper {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 2.5rem;
    position: relative;
}

.step {
    display: flex;
    align-items: center;
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.step-num {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.step.active {
    color: var(--text-primary);
}

.step.active .step-num {
    border-color: var(--primary);
    background: var(--primary);
    box-shadow: 0 0 12px var(--primary-glow);
    color: #fff;
}

.step.completed {
    color: var(--success);
}

.step.completed .step-num {
    border-color: var(--success);
    background: var(--success);
    color: #fff;
}

.step-divider {
    height: 2px;
    width: 60px;
    background: rgba(255, 255, 255, 0.1);
    margin: 0 1.5rem;
    border-radius: 1px;
}

.step-divider.active {
    background: var(--primary-gradient);
}

/* Upload Panel Styles */
.upload-panel {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

label {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
}

.select-wrapper {
    position: relative;
}

select {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    border-radius: 0.75rem;
    padding: 0.875rem 1.25rem;
    color: var(--text-primary);
    font-size: 1rem;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    transition: all 0.3s ease;
}

select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-glow);
}

select option {
    background-color: #0f172a;
    color: var(--text-primary);
}

.select-wrapper::after {
    content: '▼';
    font-size: 0.8rem;
    color: var(--text-secondary);
    position: absolute;
    right: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}

/* Import Mode Selector Tiles */
.import-mode-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .import-mode-options {
        grid-template-columns: 1fr;
    }
}

.mode-option {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--card-border);
    border-radius: 0.75rem;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mode-option:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(139, 92, 246, 0.3);
    transform: translateY(-2px);
}

.mode-option.active {
    background: rgba(139, 92, 246, 0.08);
    border-color: var(--primary);
    box-shadow: 0 0 16px rgba(139, 92, 246, 0.2);
}

.mode-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.04);
    color: var(--text-secondary);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.mode-option.active .mode-icon {
    background: var(--primary-gradient);
    color: #fff;
}

.mode-details {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.mode-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
}

.mode-desc {
    font-size: 0.8rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

/* Drag & Drop Area */
.dropzone {
    border: 2px dashed rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    padding: 4rem 2rem;
    text-align: center;
    background: rgba(255, 255, 255, 0.02);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.dropzone:hover, .dropzone.dragover {
    border-color: var(--primary);
    background: rgba(139, 92, 246, 0.05);
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.1);
}

.dropzone svg {
    width: 48px;
    height: 48px;
    color: var(--primary);
    transition: transform 0.3s ease;
}

.dropzone:hover svg {
    transform: translateY(-5px);
}

.dropzone span {
    font-weight: 500;
    color: var(--text-secondary);
}

.dropzone span strong {
    color: var(--primary);
}

.dropzone-sub {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.file-info {
    display: none;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.04);
    padding: 0.75rem 1.25rem;
    border-radius: 0.75rem;
    border: 1px solid var(--card-border);
    animation: fadeIn 0.3s ease-out;
}

.file-info svg {
    color: var(--accent);
}

.file-name {
    font-size: 0.9rem;
    font-weight: 600;
}

.file-remove {
    margin-left: auto;
    color: var(--error);
    cursor: pointer;
    font-weight: bold;
    font-size: 1.2rem;
    line-height: 1;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.875rem 2rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    gap: 0.75rem;
}

.btn-primary {
    background: var(--primary-gradient);
    color: #fff;
    box-shadow: 0 4px 14px var(--primary-glow);
}

.btn-primary:hover {
    box-shadow: 0 6px 20px var(--primary-glow);
    transform: translateY(-2px);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.08);
    color: var(--text-primary);
    border: 1px solid var(--card-border);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

/* Steps Screens Layouts */
.step-screen {
    display: none;
    animation: fadeIn 0.4s ease-out;
}

.step-screen.active {
    display: block;
}

/* Mapping & Editor Panel */
.editor-panel {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Column Mapping Section */
.mapping-grid-container {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--card-border);
    border-radius: 1rem;
    padding: 1.5rem;
}

.mapping-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--accent);
}

.mapping-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}

.mapping-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--card-border);
    border-radius: 0.75rem;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    transition: border-color 0.3s ease;
}

.mapping-card.mapped {
    border-color: rgba(16, 185, 129, 0.3);
    background: rgba(16, 185, 129, 0.02);
}

.mapping-header-name {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    font-weight: bold;
}

/* Dynamic Editor Table Grid */
.table-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.table-stats {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.table-stats span {
    font-weight: 600;
    color: var(--primary);
}

.table-container {
    overflow-x: auto;
    border: 1px solid var(--card-border);
    border-radius: 1rem;
    background: rgba(20, 26, 42, 0.4);
    max-height: 480px;
    position: relative;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    text-align: left;
}

th {
    background: #0f172a;
    color: var(--text-primary);
    font-weight: 600;
    padding: 1rem;
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 2px solid var(--card-border);
}

td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid var(--card-border);
    color: var(--text-primary);
    transition: background-color 0.2s ease;
    white-space: nowrap;
}

tr:hover td {
    background: rgba(255, 255, 255, 0.02);
}

/* Editable cells */
.editable-cell {
    cursor: pointer;
    position: relative;
    min-width: 120px;
}

.editable-cell:hover {
    background: rgba(139, 92, 246, 0.08) !important;
    border-radius: 4px;
}

.cell-input {
    width: 100%;
    background: #1e293b;
    border: 1px solid var(--primary);
    border-radius: 4px;
    color: var(--text-primary);
    padding: 0.4rem 0.6rem;
    font-size: 0.9rem;
    font-family: var(--font-sans);
    outline: none;
    box-shadow: 0 0 8px var(--primary-glow);
}

/* Cell Error Highlighting */
.cell-error {
    border: 1px solid var(--error) !important;
    background-color: var(--error-glow) !important;
    box-shadow: inset 0 0 6px rgba(239, 68, 68, 0.2);
    position: relative;
}

.cell-error::after {
    content: '!';
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--error);
    color: white;
    font-size: 0.65rem;
    font-weight: bold;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Custom CSS Tooltip */
[data-tooltip] {
    position: relative;
}

[data-tooltip]::before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%) scale(0.9);
    background: #ef4444;
    color: white;
    padding: 0.35rem 0.6rem;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    z-index: 100;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}

[data-tooltip]:hover::before {
    opacity: 1;
    transform: translateX(-50%) scale(1);
}

/* Row controls */
.row-delete-btn {
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: color 0.2s ease;
    display: flex;
    align-items: center;
}

.row-delete-btn:hover {
    color: var(--error);
}

.row-delete-btn svg {
    width: 18px;
    height: 18px;
}

/* Form Action Buttons Wrapper */
.panel-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

/* Full screen loader spinner overlay */
.loader-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(8, 12, 20, 0.85);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 1.5rem;
    animation: fadeIn 0.3s ease;
}

.spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(255, 255, 255, 0.05);
    border-top-color: var(--primary);
    border-right-color: var(--accent);
    border-radius: 50%;
    animation: spin 1s infinite linear;
    box-shadow: 0 0 20px var(--primary-glow);
}

.loader-text {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
    letter-spacing: 0.05em;
}

/* Modal styling for Success UI */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(12px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 1.5rem;
    padding: 3rem;
    max-width: 480px;
    width: 90%;
    text-align: center;
    position: relative;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
    animation: scaleUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--success);
}

.success-icon-wrapper {
    width: 80px;
    height: 80px;
    background: rgba(16, 185, 129, 0.1);
    border: 2px solid var(--success);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: var(--success);
    box-shadow: 0 0 20px var(--success-glow);
}

.success-icon-wrapper svg {
    width: 40px;
    height: 40px;
}

.modal-title {
    font-family: var(--font-heading);
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
}

.modal-description {
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 2rem;
}

/* Animations */
@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scaleUp {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Welcome Workflow Selector Dashboard */
.welcome-container {
    max-width: 800px;
    margin: 0 auto;
    width: 100%;
}

.mode-cards-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

@media (max-width: 768px) {
    .mode-cards-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

.welcome-card {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--card-border);
    border-radius: 1.25rem;
    padding: 2.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.welcome-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: transparent;
    transition: background 0.3s ease;
}

#card_mode_import::before {
    background: var(--primary-gradient);
    opacity: 0;
}

#card_mode_export::before {
    background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
    opacity: 0;
}

#card_mode_import:hover::before, #card_mode_export:hover::before {
    opacity: 1;
}

.welcome-card:hover {
    background: rgba(255, 255, 255, 0.04);
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6);
}

#card_mode_import:hover {
    border-color: rgba(139, 92, 246, 0.4);
    box-shadow: 0 20px 40px -15px rgba(139, 92, 246, 0.15);
}

#card_mode_export:hover {
    border-color: rgba(6, 182, 212, 0.4);
    box-shadow: 0 20px 40px -15px rgba(6, 182, 212, 0.15);
}

.welcome-card-icon {
    width: 70px;
    height: 70px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease;
}

.welcome-card:hover .welcome-card-icon {
    transform: scale(1.1) rotate(2deg);
}

.welcome-card-title {
    font-family: var(--font-heading);
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.welcome-card-desc {
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 2rem;
}
</style>
