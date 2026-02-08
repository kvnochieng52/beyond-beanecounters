# MTD (MTB) File Attachments Feature

## Overview
Added file attachment functionality to MTD (Money Transfer Data) records, allowing users to upload and manage supporting documents for each MTD entry.

## Features Implemented

### 1. Database
- **Migration**: `2025_02_08_084500_create_mtb_attachments_table.php`
- **New Table**: `mtb_attachments`
  - `id` - Primary key
  - `mtb_id` - Foreign key to mtbs table
  - `file_name` - Stored file name
  - `original_name` - Original file name uploaded by user
  - `file_size` - File size in bytes
  - `file_type` - MIME type
  - `file_path` - Path to stored file
  - `created_by` - User who uploaded
  - `timestamps` - Created/updated at

### 2. Models
- **MtbAttachment** (`app/Models/MtbAttachment.php`)
  - Relationships: belongsTo Mtb, belongsTo User (creator)
  - Helper methods: `getFileUrlAttribute()`, `getFileSizeFormattedAttribute()`
  
- **Mtb** - Updated with `attachments()` relationship

### 3. Controller
- **MtbController** Updated Methods:
  - `store()` - Now accepts and processes file uploads
  - `getMtbAttachments()` - Returns attachments data for DataTable
  - `downloadAttachment()` - Download attachment file
  - `deleteAttachment()` - Delete attachment and file
  - `storeAttachments()` - Private helper to store files

### 4. Routes
Added in `routes/web.php`:
```php
Route::get('/mtb/attachments/data', [MtbController::class, 'getMtbAttachments'])->name('mtb.attachments.data');
Route::get('/mtb/attachment/{id}/download', [MtbController::class, 'downloadAttachment'])->name('mtb.download-attachment');
Route::delete('/mtb/attachment/{id}', [MtbController::class, 'deleteAttachment'])->name('mtb.attachment.delete');
```

### 5. Views

#### New MTD Modal (`modals/mtb/_new_mtb_modal.blade.php`)
- Added `enctype="multipart/form-data"` to form
- Added file upload input supporting multiple files
- Accepted formats: PDF, DOC, DOCX, JPG, PNG, XLSX, XLS
- Maximum file size: 5MB per file

#### MTD Display (`lead/show/_mtb.blade.php`)
- Added "Attachments" column to MTD records table
- Added "View" button to view attachments for each MTD record
- Added modal dialog to display attachments list
- Shows: File name, type, size, uploader, upload date

#### Lead Show View (`lead/show.blade.php`)
- Updated MTB DataTable columns to include attachments
- Added JavaScript handlers:
  - `.view-attachments-btn` - Click handler to view attachments
  - `deleteAttachment()` - Function to delete attachments
  - DataTable initialization for attachments modal

### 6. File Storage
- Files stored in: `storage/app/mtb-attachments/{mtb_id}/`
- File naming: `{timestamp}_{uniqid}_{original_name}`
- Prevents file naming conflicts and tracks upload times

## Usage

### Upload Files During MTD Creation
1. Open "New MTD" modal
2. Fill in amount, date, channel, description
3. Click "Attachments (Optional)" section
4. Select one or multiple files
5. Click "Save MTD Record"

### View Attachments
1. In MTD records table, click "View Attachments" button
2. Modal opens showing all attachments for that MTD
3. Click download icon to download file
4. Click trash icon to delete attachment

### File Download
- Users can download any attachment directly
- Files keep original names
- Works cross-platform (Windows/Linux/Mac)

## Validation
- File types: PDF, DOC, DOCX, JPG, JPEG, PNG, XLSX, XLS
- Maximum size: 5MB per file
- Multiple files supported per MTD

## Security
- Files stored outside public folder (storage/app)
- Downloads go through controller (no direct access)
- CSRF protection on delete operations
- User tracking (created_by)

## Database Status
✅ Migration completed successfully
- Table created with all relationships
- Ready for production use

## Next Steps (Optional)
1. Add batch download (ZIP)
2. Add file preview for images/PDFs
3. Add file size/type validation on frontend
4. Add attachment count summary in MTD list
