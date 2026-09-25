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
