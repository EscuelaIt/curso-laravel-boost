---
paths:
  - app/Models/**
---

# Models Rules

## Use $guarded instead of $fillable

When working with Laravel models in `app/Models/**`, always use the `$guarded` property instead of `$fillable` for mass assignment protection.

### Example

```php
class User extends Model
{
    protected $guarded = [];
    // or
    protected $guarded = ['id'];
}
```

**Note:** Do not use `$fillable` in this project.

## Use SoftDeletes in new models by default
When creating a new model, always add the `SoftDeletes` trait to the model and `$table->softDeletes();` to its create-table migration (plus `deleted_at` in the `@property` PHPDoc).

Only skip soft deletes when the user explicitly says they don't want them for that model.
