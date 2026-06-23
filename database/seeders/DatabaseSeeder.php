<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@blog.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        $author = User::create([
            'name'              => 'Maya Rivers',
            'email'             => 'author@blog.com',
            'password'          => Hash::make('password'),
            'role'              => 'author',
            'bio'               => 'Senior editor covering product, culture, and practical technology.',
            'email_verified_at' => now(),
        ]);

        // Sample posts
        $posts = [
            [
                'title'    => 'Getting Started with Docker',
                'category' => 'technology',
                'image'    => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?auto=format&fit=crop&w=1200&q=80',
                'excerpt'  => 'Learn how to containerize your applications with Docker and Docker Compose.',
                'body'     => '<p>Docker is a platform for developing, shipping, and running applications in containers. Containers allow you to package an application with all its dependencies into a standardized unit.</p><p>In this post we cover the basics of Docker - images, containers, volumes, and networks. By the end you will have a solid understanding of how Docker works and why it is so widely used in modern development.</p><h2>What is a Container?</h2><p>A container is a lightweight, standalone, executable package that includes everything needed to run a piece of software: code, runtime, system tools, libraries, and settings. Containers are isolated from each other and from the host system.</p><h2>Docker Compose</h2><p>Docker Compose is a tool for defining and running multi-container applications. With a single YAML file you can configure all your services and start them with one command: <code>docker compose up</code>.</p>',
            ],
            [
                'title'    => 'Laravel for Beginners',
                'category' => 'education',
                'image'    => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80',
                'excerpt'  => 'A complete introduction to the Laravel PHP framework.',
                'body'     => '<p>Laravel is a web application framework with expressive, elegant syntax. It takes the pain out of development by making common tasks easy: routing, authentication, sessions, caching, and more.</p><p>Laravel is built on top of several Symfony components and follows the MVC pattern. It has a rich ecosystem including tools like Eloquent ORM, Blade templating, and Artisan CLI.</p><h2>Why Laravel?</h2><p>Laravel is the most popular PHP framework for good reason. It has excellent documentation, a large community, and a huge ecosystem of packages. Whether you are building a simple blog or a complex enterprise application, Laravel has the tools you need.</p><h2>Your First Route</h2><p>In Laravel, routes are defined in the <code>routes/web.php</code> file. A basic route looks like this:</p><pre><code>Route::get("/hello", function() { return "Hello World"; });</code></pre>',
            ],
            [
                'title'    => 'MySQL Performance Tips',
                'category' => 'business',
                'image'    => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
                'excerpt'  => 'Practical tips to speed up your MySQL queries and database design.',
                'body'     => '<p>MySQL is the worlds most popular open source database. With the right configuration and query design, you can get excellent performance even with large datasets.</p><h2>Use Indexes</h2><p>Indexes are the single most important tool for query performance. An index allows MySQL to find rows quickly without scanning the entire table. Always add indexes to columns you frequently use in WHERE, JOIN, and ORDER BY clauses.</p><h2>Avoid SELECT *</h2><p>Always specify the columns you need instead of using SELECT *. This reduces the amount of data transferred and allows MySQL to use covering indexes.</p><h2>Use EXPLAIN</h2><p>The EXPLAIN statement shows you how MySQL executes a query. It tells you whether indexes are being used and how many rows are being examined. This is invaluable for diagnosing slow queries.</p>',
            ],
            [
                'title'    => 'Nginx vs Apache - Which to Choose?',
                'category' => 'technology',
                'image'    => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80',
                'excerpt'  => 'A practical comparison of the two most popular web servers.',
                'body'     => '<p>Nginx and Apache are the two most widely used web servers in the world. Both are open source, battle-tested, and capable of handling high traffic. But they have different architectures and are suited to different use cases.</p><h2>Apache</h2><p>Apache uses a process-based model where each connection spawns a thread or process. This is simple and compatible with a huge number of modules, including mod_php which can execute PHP directly without a separate process.</p><h2>Nginx</h2><p>Nginx uses an event-driven, asynchronous architecture that handles thousands of connections in a single thread. This makes it extremely memory efficient and fast for serving static files and acting as a reverse proxy.</p><h2>In Docker</h2><p>In a Docker environment, both work well. A common pattern is to use Nginx as a reverse proxy in front of Apache, or to use Nginx directly with PHP-FPM - which is what we do in our Docker setup.</p>',
            ],
            [
                'title'    => 'Understanding Docker Volumes',
                'category' => 'technology',
                'image'    => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80',
                'excerpt'  => 'How to persist data in Docker containers using volumes.',
                'body'     => '<p>By default, data written inside a Docker container is lost when the container is removed. Volumes solve this problem by storing data outside the container filesystem.</p><h2>Types of Volumes</h2><p>There are two main types: bind mounts and named volumes. Bind mounts link a directory on your host machine to a path inside the container. Named volumes are managed by Docker and stored in a dedicated area on the host.</p><h2>When to Use Each</h2><p>Use bind mounts for files you want to edit directly - like your application code, config files, and nginx configs. Use named volumes for data that should persist but that you do not need to edit directly - like MySQL data files.</p>',
            ],
        ];

        foreach ($posts as $data) {
            $post = Post::create([
                'user_id'      => rand(0, 1) ? $admin->id : $author->id,
                'title'        => $data['title'],
                'excerpt'      => $data['excerpt'],
                'body'         => $data['body'],
                'image'        => $data['image'],
                'category'     => $data['category'],
                'status'       => 'published',
                'published'    => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'approved_at'  => now()->subDays(rand(1, 30)),
                'approved_by'  => $admin->id,
                'views'        => rand(75, 1600),
            ]);

            // Add a sample comment to each post
            Comment::create([
                'post_id'  => $post->id,
                'name'     => 'John Doe',
                'email'    => 'john@example.com',
                'body'     => 'Great article! Very helpful and well explained.',
                'approved' => true,
            ]);
        }
    }
}
