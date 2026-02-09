# RMS SMS Service Documentation

## Overview
The RMS SMS service provides a clean, easy-to-use interface for sending SMS messages via the MSpace API.

## Configuration

The service is configured in `config/rms_sms.php`. You can override defaults using environment variables:

```bash
RMS_SMS_USERNAME=RMSLTD
RMS_SMS_API_KEY=your_api_key_here
RMS_SMS_SENDER_ID=RMSLTD
RMS_SMS_API_ENDPOINT=https://api.mspace.co.ke/smsapi/v2/sendtext
RMS_SMS_TIMEOUT=30
```

## Installation

The service is already registered in `config/app.php`:
- Service Provider: `App\Providers\RmsSmsServiceProvider::class`
- Facade: `App\Facades\RmsSms::class` (aliased as `RmsSms`)

## Usage

### Method 1: Using the Facade (Recommended)

```php
use App\Facades\RmsSms;

// Send to single recipient
$response = RmsSms::send('0722123456', 'Hello, this is a test message');

// Send to multiple recipients
$response = RmsSms::send(['0722123456', '0733987654'], 'Hello everyone');

// Or comma-separated string
$response = RmsSms::send('0722123456,0733987654', 'Hello everyone');

// With custom sender ID
$response = RmsSms::send('0722123456', 'Message', 'CustomSender');
```

### Method 2: Dependency Injection

```php
use App\Services\RmsSmsService;

class YourController
{
    public function __construct(private RmsSmsService $sms)
    {
    }

    public function sendMessage()
    {
        $response = $this->sms->send('0722123456', 'Your message here');
        
        if ($response['success']) {
            // Handle success
        } else {
            // Handle error
        }
    }
}
```

### Method 3: Service Container

```php
app('rms-sms')->send('0722123456', 'Message');
```

## Methods

### send($recipient, $message, $senderId = null)
Send an SMS message.

**Parameters:**
- `$recipient` (string|array): Phone number(s)
- `$message` (string): Message content
- `$senderId` (string|null): Optional custom sender ID

**Returns:** Array with `success`, `data`, and `message` keys

**Example:**
```php
$response = RmsSms::send('0722123456', 'Hello!');

// Response structure:
[
    'success' => true,
    'data' => [
        'message' => [
            [
                'messageId' => '49032372',
                'recipient' => '0722123456',
                'status' => 111,
                'statusDescription' => 'Message sent successfully'
            ]
        ]
    ],
    'message' => 'SMS sent successfully'
]
```

### sendWithRetry($recipient, $message, $retries = 3, $senderId = null)
Send an SMS with automatic retry on failure.

**Parameters:**
- `$recipient` (string|array): Phone number(s)
- `$message` (string): Message content
- `$retries` (int): Number of retry attempts (default: 3)
- `$senderId` (string|null): Optional custom sender ID

**Example:**
```php
// Retry up to 3 times with exponential backoff
$response = RmsSms::sendWithRetry('0722123456', 'Important message', 3);
```

### parseResponse($response)
Parse and validate API response.

**Parameters:**
- `$response` (array): Raw API response

**Returns:** Parsed response with validation status

**Example:**
```php
$parsed = RmsSms::parseResponse($apiResponse);

// Response structure:
[
    'success' => true,
    'total_messages' => 2,
    'failed_count' => 0,
    'messages' => [...]
]
```

## Response Statuses

The API returns status codes in responses:
- **111**: Message sent successfully
- Other codes indicate failure

## Error Handling

```php
$response = RmsSms::send('0722123456', 'Message');

if ($response['success']) {
    // Parse the detailed response
    $parsed = RmsSms::parseResponse($response['data']);
    
    if ($parsed['success']) {
        echo "All messages sent successfully";
    } else {
        echo "Some messages failed: " . $parsed['failed_count'];
    }
} else {
    // Connection or validation error
    echo "Error: " . $response['message'];
    Log::error('SMS send failed', $response);
}
```

## Logging

All SMS sends are logged to Laravel's default logger:
- **Success**: `Log::info('SMS sent successfully', [...])`
- **Failure**: `Log::error('Failed to send SMS', [...])`

Check logs in `storage/logs/laravel.log`

## Examples

### Send OTP
```php
$otp = rand(100000, 999999);
RmsSms::send($userPhone, "Your OTP is: $otp");
```

### Notification to Multiple Users
```php
$phoneNumbers = ['0722111111', '0733222222', '0745333333'];
RmsSms::send($phoneNumbers, 'Important account notification');
```

### Integration with Model Events
```php
use App\Models\Lead;

class Lead extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($lead) {
            RmsSms::send(
                $lead->phone,
                "Welcome! Your ticket number is {$lead->id}"
            );
        });
    }
}
```

### Queue the Send (Recommended for High Volume)
```php
// Create a job to queue SMS sends
use App\Jobs\SendSmsJob;

dispatch(new SendSmsJob('0722123456', 'Your message'));
```

## Troubleshooting

### "Call to undefined method" Error
- Ensure `RmsSmsServiceProvider` is registered in `config/app.php`
- Run `php artisan config:cache` and `php artisan view:clear`

### SMS Not Sending
- Verify API key is correct in `.env`
- Check network connectivity
- Review logs in `storage/logs/laravel.log`
- Verify phone number format (e.g., 0722123456 or 254722123456)

### API Response Errors
- Status != 111 indicates API error
- Check MSpace API documentation for error codes
- Verify message doesn't exceed character limits

## Best Practices

1. **Always use try/catch or check response['success']**
2. **For high volume, queue the SMS sends**
3. **Validate phone numbers before sending**
4. **Log all SMS for compliance/audit**
5. **Use retry for critical messages**
6. **Store sent SMS records in database**

## Configuration in .env

```bash
APP_NAME='Beyond Debt'
RMS_SMS_USERNAME=RMSLTD
RMS_SMS_API_KEY=your_generated_api_key
RMS_SMS_SENDER_ID=RMSLTD
RMS_SMS_API_ENDPOINT=https://api.mspace.co.ke/smsapi/v2/sendtext
RMS_SMS_TIMEOUT=30
```
