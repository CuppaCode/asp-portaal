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
