# Commands

```bash
# -m migrate
# -f factory
php artisan make:model <model> -mf
php artisan make:controller <controller> --model=<model>

php artisan make:migration create_post_tag_table
php artisan migrate

php artisan migrate:fresh --seed
# https://laravel.com/docs/13.x/filesystem#the-public-disk
php artisan storage:link

php artisan make:observer <name>
```
