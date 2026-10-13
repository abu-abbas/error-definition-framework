## Error Definition Framework (abu-abbas/error-definition-framework)

- Every application error is a case of a string-backed enum that implements `Edf\Core\ErrorCode`, with one `#[Edf\Core\Attributes\ErrorDefinition(...)]` attribute on the case. Never hardcode error codes or error messages anywhere else.
- Throw application errors with `Edf\Core\Exceptions\ApplicationException::of(SomeError::CASE, context: [...])`, not with `abort()` or ad-hoc JSON responses.
- FormRequests that opt in with `Edf\Laravel\Validation\HasErrorDefinitions` must map every rule in `errorCodes()` and must not define `messages()`.
- After changing error enums or FormRequests, run `php artisan error-definition:lint` and `php artisan error-definition:generate`. Never edit the generated `error-codes.ts` or `error-catalog.json` by hand.
- Activate the `error-definition-development` skill for the full workflow.
