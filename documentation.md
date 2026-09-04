# Read and understand

## API
1. Why use apiResource facade?
 // because it already provides CRUD routes out of the box

2. Why use decimal as amount in payable/receivable schemas? couldnt it be a string? look up
    // $table->decimal('amount', 10, 2)


# Controllers
1. /Http/Controllers/Api

# Requests & Validations
1. /Http/Requests/Auth/LoginRequest.php
2. /Http/Requests/*

# Services
1. /Services/FinancialLedgerService.php
2. /Services/DashboardService.php
3. /Services/ReportService.php

## Other
1. /Enums/*
2. /Concerns/HasOverdueStatus.php
3. /Exceptions/PartyHasTransactionsException.php
4. /Rules/ValidDocumentForType.php


# Frontend