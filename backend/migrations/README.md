# Database Migrations

This directory contains SQL migration files for the sheet music store database.

## Setup Instructions

### 1. Create the Database

First, create the database if it doesn't exist:

```sql
CREATE DATABASE IF NOT EXISTS sheet_music_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sheet_music_store;
```

### 2. Run the User Profiles Migration

Execute the `create_user_profiles_table.sql` migration to create the user profiles table:

```bash
mysql -u root -p sheet_music_store < create_user_profiles_table.sql
```

Or manually run the SQL commands in your MySQL client:

```sql
USE sheet_music_store;
SOURCE create_user_profiles_table.sql;
```

### 3. Verify the Tables

Check that the tables were created successfully:

```sql
SHOW TABLES;
DESCRIBE user_profiles;
DESCRIBE users;
```

## Migration Details

### create_user_profiles_table.sql

This migration:
- Creates the `user_profiles` table with billing and shipping address fields
- Adds a `name` column to the `users` table if it doesn't exist
- Updates existing users to populate the `name` field from `first_name` and `last_name`
- Sets up foreign key constraints and indexes

## API Endpoints

After running the migration, the following API endpoints will be available:

### GET /api/profile
Get the current user's profile information.

**Authentication:** Required (Bearer token)

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "billing_address": {
    "street": "123 Main St",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA",
    "phone": "+1234567890"
  },
  "shipping_address": {
    "street": "456 Oak Ave",
    "city": "Brooklyn",
    "state": "NY",
    "postal_code": "11201",
    "country": "USA",
    "phone": "+1234567890"
  }
}
```

### PUT /api/profile
Update the current user's profile information.

**Authentication:** Required (Bearer token)

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "billing_address": {
    "street": "123 Main St",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA",
    "phone": "+1234567890"
  },
  "shipping_address": {
    "street": "456 Oak Ave",
    "city": "Brooklyn",
    "state": "NY",
    "postal_code": "11201",
    "country": "USA",
    "phone": "+1234567890"
  }
}
```

**Response:**
```json
{
  "message": "Profile updated successfully.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "billing_address": { ... },
    "shipping_address": { ... }
  }
}
```

## Troubleshooting

### Error: Table 'users' doesn't exist

Make sure you have created the `users` table first. The user profiles table depends on it.

### Error: Duplicate column name 'name'

This is safe to ignore. The migration uses `ADD COLUMN IF NOT EXISTS` to prevent errors if the column already exists.

### Foreign Key Constraint Fails

Ensure that the `users` table exists and has an `id` column as the primary key before running this migration.
