# Bulk Update Leads Documentation

## Overview

The Bulk Update Leads utility allows you to update multiple lead records simultaneously using a CSV file. This tool is designed to update existing leads only - it does not create new leads.

## How It Works

1. **Ticket-based Updates**: The system uses the `ticket_no` column (which corresponds to the Lead ID) to identify which records to update.
2. **Selective Updates**: Only fields with values in the CSV will be updated. Empty fields are ignored.
3. **Lookup Resolution**: For reference fields (like gender, country, etc.), you provide the name and the system looks up the corresponding ID.
4. **Validation**: The system validates that all lookup values exist before updating any record.

## Access

Navigate to: **Leads > Bulk Update Leads** in the sidebar menu.

## Step-by-Step Usage

### 1. Download Template
- Click "Download Template" to get a CSV file with all supported columns
- The template includes sample data to help you understand the format

### 2. Prepare Your CSV File
- **Required Column**: `ticket_no` (Lead ID) - must be the first column
- **Format**: Standard CSV format with comma separators
- **File Size**: Maximum 10MB
- **Encoding**: UTF-8 recommended

### 3. Fill Your Data
- Only include data for fields you want to update
- Leave fields empty if you don't want to change them
- Use exact names for lookup fields (see reference tables below)

### 4. Upload and Process
- Select your CSV file
- Click "Upload & Process"
- Review the results page for success/error details

## Supported Fields

### Direct Fields (Enter values directly)
- `title` - Lead name/title
- `id_passport_number` - ID/Passport number
- `account_number` - Account number
- `telephone` - Primary telephone
- `alternate_telephone` - Secondary telephone
- `email` - Primary email
- `alternate_email` - Secondary email
- `town` - Town/City
- `address` - Physical address
- `occupation` - Occupation
- `company_name` - Company name
- `description` - Description
- `kin_full_names` - Next of kin full names
- `kin_telephone` - Next of kin telephone
- `kin_email` - Next of kin email
- `kin_relationship` - Relationship to next of kin
- `amount` - Principal amount (numeric)
- `additional_charges` - Additional charges (numeric)
- `balance` - Outstanding balance (numeric)
- `waiver_discount` - Waiver discount (numeric)
- `due_date` - Due date (YYYY-MM-DD format)
- `last_ptp_amount` - Last PTP amount (numeric)
- `last_ptp_date` - Last PTP date (numeric)
- `last_retire_date` - Last retire date (numeric)

### Lookup Fields (Enter the exact name as it appears in the system)
- `defaulter_type_name` - Individual, Corporate, etc.
- `gender_name` - Male, Female, etc.
- `country_name` - Kenya, Uganda, etc.
- `institution_name` - Name of the institution
- `currency_name` - KES, USD, etc.
- `status_name` - Active, Inactive, etc.
- `stage_name` - New Lead, Follow Up, etc.
- `category_name` - High Value, Low Value, etc.
- `priority_name` - High, Medium, Low, etc.
- `industry_name` - Technology, Agriculture, etc.
- `conversion_status_name` - Qualified, Unqualified, etc.
- `engagement_level_name` - High, Medium, Low, etc.
- `assigned_agent_name` - Agent name or agent code
- `assigned_department_name` - Collections, Sales, etc.
- `call_disposition_name` - Contact Made, No Answer, etc.

## Example CSV Structure

```csv
ticket_no,title,telephone,email,amount,balance,country_name,status_name
277,Updated Company Name,0712345678,newemail@company.com,1200000.00,1200000.00,Kenya,Active
278,,0725336212,,,,,
279,,,,350000.00,350000.00,,
```

In this example:
- Lead 277: Updates title, telephone, email, amounts, country, and status
- Lead 278: Only updates telephone (other fields left unchanged)
- Lead 279: Only updates amount and balance

## Important Notes

### Data Validation
- **Ticket Number**: Must exist in the system
- **Lookup Values**: Must match exactly (case-sensitive)
- **Dates**: Use YYYY-MM-DD format (e.g., 2025-12-31)
- **Numbers**: Use decimal format (e.g., 1000.50)

### Error Handling
- If a lookup value is not found, the entire row update is skipped
- Invalid dates or numbers will cause row updates to fail
- Missing ticket numbers will skip the row

### Security & Permissions
- Only users with "Create Lead" permission can access this tool
- All updates are logged with the user who performed them
- The `updated_by` and `updated_at` fields are automatically set

### Performance
- Process up to 1000 records efficiently
- Larger files may take longer to process
- Results are displayed in real-time after processing

## Troubleshooting

### Common Errors
1. **"Lead with ticket number X not found"**
   - Verify the ticket number exists in the system
   - Check for typos in the ticket_no column

2. **"Gender 'XYZ' not found"**
   - Check the exact spelling and case of lookup values
   - Ensure the lookup value is active in the system

3. **"Invalid date format"**
   - Use YYYY-MM-DD format for dates
   - Example: 2025-12-31

4. **"Missing required columns"**
   - Ensure your CSV has the `ticket_no` column
   - Download the template for the correct format

### Best Practices
1. **Test with Small Batches**: Start with 5-10 records to verify your format
2. **Backup Data**: Consider backing up before large updates
3. **Verify Lookup Values**: Check that all lookup values exist in the system
4. **Review Results**: Always check the results page for any errors

## Result Interpretation

The results page shows:
- **Success Count**: Number of successfully updated records
- **Error Count**: Number of records with errors
- **Details Table**: Row-by-row breakdown with specific error messages

### Success Messages
- Lists which fields were updated for each record
- Confirms the ticket number and row number

### Error Messages
- Specific reason why the update failed
- Row number for easy CSV reference
- Suggested corrections when applicable

## Technical Details

### File Processing
- CSV files are processed row by row
- Headers are validated before processing begins
- Updates are performed individually (not in bulk transactions)

### Database Updates
- Only specified fields are updated (no full record replacement)
- Timestamp fields are automatically updated
- Foreign key constraints are respected

### Lookup Resolution
- Case-sensitive matching for all lookup fields
- Only active records are considered for lookups
- Agent lookup supports both name and agent code

This tool provides a powerful way to maintain your leads data efficiently while ensuring data integrity and providing clear feedback on the update process.