This is the Official Documentations of GReAT System


Great System Documentation:
Vehicle Franchising Sections:

File Name: PaymentController.php
    This controller functions is:
    index() → Lists all payments with filters (search, method, status, year) + summary stats (total collected, today's total, voided count)
    create() → Shows the new OR form, pre-loads an optional franchise, collection natures, and the user's assigned AF51 booklet
    store() → Validates and saves a new payment (OR), verifies the OR number belongs to the user's assigned booklet, filters zero-amount rows, then redirects to print
    show() → Displays a single payment record with its related franchise, vehicle, TODA, and collector
    printReceipt() → Renders the printable AF51 receipt view for a given payment
    soa() → Shows a Statement of Account for a specific franchise — all its payments, filterable by year, with paid/voided totals
    renew() → Processes a franchise renewal — records a new payment, updates the franchise's permit number/date/type, and logs the action to franchise history
    void() → Marks a paid OR as voided with an optional reason




