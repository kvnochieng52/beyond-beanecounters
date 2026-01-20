# PTP Reminder System Documentation

## Overview
The PTP (Promise to Pay) Reminder System automatically sends SMS reminders to customers who have promised to make payments on specific dates.

## Components

### 1. Database Table: `reminder_processes`
Tracks all reminder processes with the following fields:
- `process_type`: Type of reminder process (default: 'ptp_reminder')
- `process_date`: Date the reminders are for
- `start_time`/`end_time`: Process execution times
- `total_customers`: Number of customers processed
- `successful_reminders`/`failed_reminders`: Success/failure counts
- `status`: Process status (pending, running, completed, failed)
- `processed_customers`: JSON array of processed customer details

### 2. Job: `PTPReminderJob`
- **Location**: `app/Jobs/PTPReminderJob.php`
- **Purpose**: Sends SMS reminders to PTP customers
- **Logic**:
  - Finds activities with `activity_type_id = 4` (PTP) due on specified date
  - Generates personalized SMS messages
  - Sends SMS using existing BSms system
  - Creates activity records for sent reminders
  - Tracks success/failure rates

### 3. Controller: `ReminderProcessController`
- **Location**: `app/Http/Controllers/ReminderProcessController.php`
- **Routes**: `/reminder-processes/*`
- **Features**:
  - View all reminder processes
  - Schedule new reminders
  - Run reminders immediately
  - View detailed process results

### 4. Views
- **Index**: `resources/views/reminder_processes/index.blade.php`
- **Create**: `resources/views/reminder_processes/create.blade.php`
- **Show**: `resources/views/reminder_processes/show.blade.php`

### 5. Artisan Command: `SendPTPReminders`
- **Command**: `php artisan ptp:send-reminders`
- **Options**: `--date=YYYY-MM-DD` (defaults to today)
- **Usage**: 
  ```bash
  # Send reminders for today
  php artisan ptp:send-reminders
  
  # Send reminders for specific date
  php artisan ptp:send-reminders --date=2025-01-20
  ```

## Message Template
The reminder message format:
```
Dear [customer title], Remember to make your [client name] debt payment of KES [amount] today. Paybill [paybill number], account [account number].
```

## How It Works

1. **Finding PTP Customers**: The system queries the `activities` table for records with:
   - `activity_type_id = 4` (Call Made - Promised to Pay)
   - `due_date_time` matching the target date
   - **Lead's institution must have `client_contract_type_id = 1`** (Direct contract type)

2. **Message Generation**: For each customer, the system:
   - Retrieves lead details (name, institution, balance, paybill info)
   - Generates personalized SMS message
   - Uses the existing BSms system to send

3. **Tracking**: Every reminder process is tracked with:
   - Start/end times
   - Success/failure counts
   - Detailed customer processing results
   - Error messages for failures

## Web Interface

Access the reminder management interface at:
- **List all processes**: `/reminder-processes`
- **Schedule new reminder**: `/reminder-processes/create`
- **View process details**: `/reminder-processes/{id}`

## Automation

To automate daily PTP reminders, add this to your cron job:
```bash
# Run every day at 9:00 AM
0 9 * * * cd /path/to/your/project && php artisan ptp:send-reminders
```

## Testing

To test the system:
1. Ensure you have PTP activities (`activity_type_id = 4`) with due dates
2. **Verify the associated leads belong to institutions with `client_contract_type_id = 1`**
3. Run: `php artisan ptp:send-reminders --date=TODAY`
4. Monitor the process at `/reminder-processes`

## Notes

- The system uses the existing SMS infrastructure (BSms model)
- Activity records are created for each sent reminder
- Failed reminders are logged with error details
- The system handles missing customer data gracefully
- **Only leads from institutions with `client_contract_type_id = 1` will receive reminders**
