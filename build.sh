#!/usr/bin/env bash
set -o errexit

# نصب درایور PostgreSQL برای PHP
apt-get update
apt-get install -y php-pgsql