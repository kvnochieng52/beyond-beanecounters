<div style="font-family: Arial, sans-serif; color: #333;">
    <div style="background-color: #f8f9fa; padding: 20px;">
        <div
            style="max-width: 800px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

            <h2 style="color: #1f4e78; margin-bottom: 20px; border-bottom: 3px solid #1f4e78; padding-bottom: 10px;">
                📊 Agent Weekly Report
            </h2>

            <p style="color: #666; line-height: 1.6; font-size: 14px;">
                Hello <strong>{{ $user->name }}</strong>,
            </p>

            <p style="color: #666; line-height: 1.6; font-size: 14px;">
                Please find attached the <strong>Agent Weekly Report</strong> for the period
                <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong>,
                generated on <strong>{{ $generatedAt->format('d M Y g:i A') }}</strong>.
            </p>

            <div
                style="background-color: #e7f3ff; border-left: 4px solid #0066cc; padding: 15px; margin: 20px 0; border-radius: 3px;">
                <p style="color: #0066cc; margin: 0 0 10px 0; font-size: 14px;">
                    <strong>📝 Report Contents:</strong>
                </p>
                <ul style="color: #666; margin: 0; padding-left: 20px; font-size: 13px;">
                    <li>Calls Made - Total call dispositions by agent for the week</li>
                    <li>PTP Count & Value - Promise to Pay registrations and amounts</li>
                    <li>Collections - Weekly transaction and MTD (Money Transfer Data) totals</li>
                    <li>Institution Breakdown - Collections by agent per institution</li>
                </ul>
            </div>

            <div
                style="background-color: #fef3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 3px;">
                <p style="color: #856404; margin: 0; font-size: 13px;">
                    <strong>⏰ Report Schedule:</strong> This automated report is sent daily at 8:30 AM and covers the
                    past 7 days of activity.
                </p>
            </div>

            <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 20px 0;">

            <p style="color: #999; font-size: 12px; line-height: 1.6;">
                This is an automated system-generated report. Please do not reply to this email.<br>
                <strong>{{ config('app.name') }}</strong> | Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}
            </p>

        </div>
    </div>
</div>
