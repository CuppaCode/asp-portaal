# Claim Form Module Tests

## Test Suite Status

**✅ 7 / 15 tests passing | ⨯ 8 tests failing**

The `PublicClaimFormTest` provides comprehensive testing for the public claim form module.

### Passing Tests ✅ (7)
- ✅ Display claim form with valid token
- ✅ Display company logo on form
- ✅ Reject invalid tokens
- ✅ Reject inactive tokens
- ✅ Validate required fields
- ✅ Apply field width classes (full, half, third, quarter)
- ✅ Increment token usage counter

### Failing Tests ⨯ (8)
- ⨯ Submit claim form with draft status - needs all required fields configured
- ⨯ Groups fields correctly - field grouping not visible in HTML (fields not enabled)
- ⨯ Approve draft claim with signed URL - needs complete claim data
- ⨯ Display deny form with signed URL - needs complete claim data
- ⨯ Deny draft claim with reason - needs complete claim data
- ⨯ Validate deny reason minimum length - needs complete claim data
- ⨯ Reject unsigned approve URL - needs complete claim data
- ⨯ Reject expired signed URL - needs complete claim data

## Running the Tests

### Run all claim form tests:
```bash
php artisan test --filter PublicClaimFormTest
```

### Run a specific test:
```bash
php artisan test --filter "PublicClaimFormTest::it_displays_claim_form_with_valid_token"
```

### Run with verbose output:
```bash
php artisan test --filter PublicClaimFormTest --verbose
```

## Test Coverage

### Form Display & Access
- Form loads with valid token
- Company logo displays correctly
- Invalid/inactive tokens return 404
- Token usage tracked on access

### Layout & Styling
- Field width classes applied (full, half, third, quarter)
- Fields grouped correctly in HTML structure
- Responsive layout

### Form Validation
- Required fields validated
- Field types checked

### Draft Claim Workflow (Partial)
- Signed URL structure
- Approval/denial forms
- Status updates

## Test Data

Tests use factories and direct DB inserts to create:
- Companies with logos
- Claim form tokens (active/inactive)
- Claims with different statuses  
- Form field configurations
- Custom fields with layout options

## Database Setup

The tests use `RefreshDatabase` trait:
- Automatically migrates fresh database before tests
- Rolls back after each test
- No manual cleanup needed

## Known Limitations

1. **Role/User Setup**: Tests avoid creating users due to complex role attachment in User model constructor
2. **Form Submission**: Requires all standard claim form fields to be properly configured
3. **Notifications**: Not tested to avoid user/role dependencies

## Extending the Tests

To add more tests:

1. Add test method in `PublicClaimFormTest.php`:
```php
/** @test */
public function it_does_something()
{
    // Arrange
    $data = [...];
    
    // Act  
    $response = $this->get(route(...));
    
    // Assert
    $response->assertStatus(200);
    $this->assertDatabaseHas('table', [...]);
}
```

2. Run your new test:
```bash
php artisan test --filter it_does_something
```

## Test Files

- `tests/Feature/PublicClaimFormTest.php` - Main test suite
- `database/factories/CompanyFactory.php` - Company test data
- `database/factories/ClaimFactory.php` - Claim test data with statuses

## CI/CD Integration

Add to your pipeline:
```yaml
- name: Run Claim Form Tests
  run: php artisan test --filter PublicClaimFormTest
```

## Manual Regression Checklist: Claim Mail Attachments

Scope: claim dossier mail tab for logged-in users, including attachment size/count restrictions and attachment editing before submit.

### Preconditions
1. Logged in as admin or agent.
2. Open a claim dossier and switch to the Mail tab.
3. Ensure server upload limits are at least as high as app limits for this environment.

### Attachment Queue Behavior (Remove/Add/Replace)
1. Add 2 attachments and verify they appear in the selected attachments list.
2. Remove 1 attachment and verify it disappears from the list and is not submitted.
3. Add a new attachment after removal and verify it is appended and accepted.
4. Replace an attachment using the "Vervangen" action and verify the file name and size update correctly.
5. Use "Alle bijlagen wissen" and verify the list resets to no selected files.

### Size/Count Validation (Backoffice)
1. Upload 1 file larger than configured backoffice mail max size (default 25 MB) and verify submit is blocked with a clear error.
2. Upload more than configured max file count (default 20) and verify submit is blocked with a clear error.
3. Upload unsupported extension and verify validation message is shown.
4. Upload valid files within limits and verify submit succeeds.

### Oversized Request Recovery (PostTooLarge)
1. Simulate request body larger than server POST limit.
2. Verify user is redirected back to same claim page.
3. Verify mail tab is active after redirect.
4. Verify a clear recovery error is shown for attachments.

### Public Claim Form Strictness
1. Open token-based public claim form (not logged in).
2. Upload a file larger than public max size (default 10 MB) and verify it is rejected.
3. Upload more than public max file count (default 10) and verify it is rejected.
4. Verify allowed extension checks are still enforced.

### Activity Recovery
1. Send a valid mail so a mail note appears in activities.
2. Verify note delete button is visible only with note delete permission.
3. Delete the note and verify it is removed from the activities list.

### Suggested Follow-up Automation
1. Add feature tests for claim mail request validation rules in the send endpoint.
2. Add browser test coverage for client-side add/remove/replace queue behavior.
3. Add browser test for redirect and error visibility on oversized requests.
