# PTP Reminder System - Automated Daily Schedule

## Automatic Daily Reminders at 8:00 AM

The PTP reminder system is now configured to automatically send reminders every day at 8:00 AM for customers with due dates on that day.

## Setup Instructions

### 1. Laravel Scheduler Setup

Add the following cron job to your server:

```bash
# Edit crontab
crontab -e

# Add this line (run every minute to check Laravel scheduler)
* * * * * cd /path/to/your/laravel-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. What Happens Automatically

Every day at **8:00 AM**, the system will:
1. ✅ Find all PTP activities (`activity_type_id = 4`) due today
2. ✅ Filter by institutions with `client_contract_type_id = 1`
3. ✅ Send personalized SMS reminders
4. ✅ Track the process in the database
5. ✅ Create activity records for each sent reminder

### 3. Monitoring Daily Reminders

Visit `/reminder-processes` to see:
- **Today's Reminders Section** - Prominently displayed at the top
- **Real-time Status** - Shows if today's reminders are completed, running, or failed
- **Success Metrics** - Total customers, successful, failed, and success rate
- **Manual Control** - Run or retry today's reminders manually

### 4. Manual Commands

If you need to run reminders manually:

```bash
# Run today's reminders immediately
php artisan ptp:daily-reminders

# Run reminders for specific date
php artisan ptp:send-reminders --date=2025-01-20

# Run via web interface
# Visit /reminder-processes and click "Run Now"
```

### 5. Web Interface Features

#### Today's Reminders Dashboard
- **Status Badge**: Shows current status (Completed, Running, Failed, Pending)
- **Statistics Cards**: Total customers, successful, failed, success rate
- **Quick Actions**: Run now, view details, retry failed processes

#### Process Details
- **Customer List**: All processed customers with status
- **Error Tracking**: Detailed error messages for failed reminders
- **Timing Information**: Start/end times and duration

### 6. Troubleshooting

#### If reminders don't run automatically:
1. Check if cron job is set up correctly
2. Verify Laravel scheduler is working: `php artisan schedule:run`
3. Check logs for any errors

#### If reminders fail:
1. Check the process details at `/reminder-processes`
2. Review error messages in the process view
3. Verify SMS configuration (BSms system)
4. Check if customers have valid phone numbers

### 7. Message Template

The automated message sent to customers:
```
Dear [customer title], Remember to make your [institution name] debt payment of KES [amount] today. Paybill [paybill number], account [account number].
```

### 8. Filtering Logic

Only customers meeting these criteria receive reminders:
- ✅ Have PTP activity (`activity_type_id = 4`) due today
- ✅ Belong to institution with `client_contract_type_id = 1`
- ✅ Have valid telephone number
- ✅ Have valid lead information

## Benefits

- **Automated**: No manual intervention required
- **Reliable**: Built-in error handling and retry mechanisms
- **Trackable**: Complete audit trail of all reminder processes
- **Flexible**: Manual override options when needed
- **Efficient**: Only processes relevant customers (contract type filtering)

The system is now fully automated and will run daily without any manual intervention!
