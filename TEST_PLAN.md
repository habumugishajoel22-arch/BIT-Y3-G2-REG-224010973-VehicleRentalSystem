# Vehicle Rental System — Test Plan

## 1. Purpose

This test plan describes manual tests for the Vehicle Rental System. It covers account access, vehicle records, customer records, rental operations, validation, and role permissions.

## 2. Test Environment

- Application: Vehicle Rental System
- Server: XAMPP Apache
- Backend: PHP
- Database: MySQL (`vehicle_rental`)
- Browser: Any current browser
- Test data: Import `database/schema.sql`, then `database/seed.sql`

Use the demo accounts listed in `README.md`. Perform tests on a development database, not on real customer data.

## 3. Entry Conditions

Before testing:

1. Apache and MySQL are running.
2. The `vehicle_rental` database and tables have been created.
3. Sample data has been imported.
4. The application opens at `http://localhost/vehicle-rental/public/`.
5. The tester can sign in with the demo admin and staff accounts.

## 4. Test Cases

| ID | Area | Test / Steps | Expected Result |
|---|---|---|---|
| TC-01 | Login | Sign in with the valid admin username and password. | Admin is authenticated and redirected to the dashboard. | ![TC1](docs/test01.png)|

| TC-02 | Login | Enter an incorrect username or password. | Login is rejected and an invalid-credentials message is shown. |![TC1](docs/test02.png)

| TC-03 | Logout | Sign out using the logout control. | Session ends and the user is returned to the login page. |![TC1](docs/test03.png)

| TC-04 | Registration | Register with valid username, email, password (at least 8 characters), and matching confirmation. | Account is created and the user can proceed to login. | ![TC1](docs/test04.png) 

| TC-05 | Registration | Register with passwords that do not match. | Registration is rejected and a validation message is shown. |  ![TC1](docs/test05.png)

| TC-06 | Registration | Try to register with an already-used username or email. | Duplicate account is rejected with an error message. | ![TC1](docs/test06.png)

| TC-07 | Dashboard | Sign in and open the dashboard. | Vehicle, customer, rental summaries and recent rentals are displayed. |![TC1](docs/test07.png)

| TC-08 | Vehicles | Add a vehicle with a valid plate number, model, type, positive daily rate, and status. | Vehicle is saved and appears in the vehicle list. |![TC1](docs/test08.png)

| TC-09 | Vehicles | Add another vehicle using an existing plate number. | Duplicate plate number is rejected. | ![TC1](docs/test09.png)

| TC-10 | Vehicles | Submit a vehicle with an invalid or empty plate/model/type/rate. | Invalid data is rejected with validation messages.

| TC-11 | Vehicles | Search for a vehicle by plate number or model. | Matching vehicles are displayed. |![TC1](docs/test10.png)

| TC-12 | Vehicles | Filter vehicles by available, rented, or maintenance status. | Only vehicles with the selected status are displayed. |![TC1](docs/test12A.png)

| TC-13 | Vehicles | Edit a vehicle that is not currently rented. | Updated vehicle details are saved and displayed. |![TC1](docs/test13.png)

| TC-14 | Vehicles | Attempt to delete a vehicle that has rental records. | Deletion is prevented and an explanatory message is shown.

| TC-15 | Customers | Add a customer with valid name, unique licence number, phone, and optional valid email. | Customer is saved and appears in the customer list. |![TC1](docs/test15.png)
| TC-16 | Customers | Add a customer using an existing licence number. | Duplicate licence number is rejected. |![TC1](docs/test16.png)

| TC-17 | Customers | Submit an invalid phone number or invalid email address. | Invalid data is rejected with validation messages. | ![TC1](docs/test17.png)

| TC-18 | Customers | Search for a customer by name, licence number, phone, or email. | Matching customer records are displayed. |![TC1](docs/test18.png)

| TC-19 | Customers | Edit an existing customer. | Updated customer information is saved. |![TC1](docs/test19.png)
| TC-20 | Customers | Attempt to delete a customer who has rental records. | Deletion is prevented and an explanatory message is shown. |![TC1](docs/test20.png)

| TC-21 | Rentals | Create a rental using an available vehicle, an existing customer, and valid dates. | Rental is created, cost is calculated, and vehicle status changes to rented. |![TC1](docs/test21.png)

| TC-22 | Rentals | Try to create a rental for a vehicle that is not available. | Rental is rejected and the vehicle remains unchanged. |

| TC-23 | Rentals | Create a rental with an invalid date or an end date before the start date. | Rental is rejected with a validation message. |![TC1](docs/test23.png)

| TC-24 | Rentals | Check the rental cost for a known daily rate and date range. | Cost equals daily rate multiplied by the number of rental days, with a minimum of one day. |

| TC-25 | Rentals | Edit an active rental's customer or dates. | Changes are saved and the rental cost is recalculated. |![TC1](docs/test25.png)

| TC-26 | Rentals | Mark an active rental as returned. | Rental status becomes returned and the vehicle becomes available. |![TC1](docs/test26.png)

| TC-27 | Rentals | Cancel an active rental. | Rental status becomes cancelled and the vehicle becomes available. |![TC1](docs/test27.png)

| TC-28 | Rentals | Try to edit or close a rental that is already returned or cancelled. | The operation is not allowed. |

| TC-29 | Permissions | Sign in as staff and try to open the user-management pages. | Access is denied because user management is admin-only. |

| TC-30 | Permissions | Sign in as admin and open user management. | Admin can view and manage user accounts. |![TC1](docs/test30.png)

| TC-31 | Permissions | As admin, attempt to delete the currently signed-in account. | The application prevents the admin from deleting their own account. |![TC1](docs/test31.png)

## 5. Test Result Record

For each test case, record the result after running it:

- **Pass:** Actual result matches the expected result.
- **Fail:** Actual result does not match the expected result.
- **Blocked:** The test could not be completed because of an environment or setup problem.

| Test Case ID | Actual Result / Notes | Status (Pass / Fail / Blocked) | Tester / Date |
|---|---|---|---|
| TC-01 |![TC1](docs/test01.png)|  
| TC-02 | ![TC1](docs/test02.png) |  |  |
| TC-03 | ![TC1](docs/test03.png) |  |  |
| TC-04 | ![TC1](docs/test04.png) |  |  |
| TC-05 | ![TC1](docs/test05.png) |  |  |
| TC-06 | ![TC1](docs/test06.png) |  |  |ooo
| TC-07 | ![TC1](docs/test07.png) |  |  |
| TC-08 | ![TC1](docs/test08.png) |  |  |
| TC-09 | ![TC1](docs/test09.png) |  |  |
| TC-10 |![TC1](docs/test01.png)  |  |  |
| TC-11 | ![TC1](docs/test10.png) |  |  |
| TC-12 | ![TC1](docs/test11.png) |  |  |


| TC-13 | ![TC1](docs/test13.png) |  |  |
| TC-14 |  |  |  |
| TC-15 | ![TC1](docs/test15.png) |  |  |
| TC-16 | ![TC1](docs/test16.png) |  |  |
| TC-17 |![TC1](docs/test17.png)  |  |  |
| TC-18 | ![TC1](docs/test18.png) |  |  |
| TC-19 | ![TC1](docs/test19.png) |  |  |
| TC-20 |  ![TC1](docs/test20.png)|  |  |
| TC-21 | ![TC1](docs/test21.png) |  |  |
| TC-22 |   |  |  |
| TC-23 | ![TC1](docs/test023.png) |  |  |
| TC-24 |  |  |  |
| TC-25 | ![TC1](docs/test25.png) |  |  |
| TC-26 | ![TC1](docs/test26.png) |  |  |
| TC-27 ![TC1](docs/test27.png)|  |  |  |
| TC-28 |  |  |  |
| TC-29 |  |  |  |
| TC-30 | ![TC1](docs/test30.png) |  |  |
| TC-31 | ![TC1](docs/test31.png) |  |  |

## 6. Exit Criteria

Testing is complete when all planned test cases have been attempted, each result has been recorded, and any failed or blocked tests have been documented for correction or follow-up.

## 7. Limitations

This is a manual test plan based on the current project files and implemented application logic. The test cases are proposed checks; they have not been executed as part of preparing this document. Record actual results only after testing the running application.
