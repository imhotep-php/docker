SELECT 'CREATE DATABASE imhotep'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'imhotep')\gexec