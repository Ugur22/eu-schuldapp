## GET

### no tokens

`/locations`
**get locations/place name**

`/client/status`
**clients' statusses**

`/company-types`
**get company types: debt collector, employer, enz**

`/upload-options`
**get other document upload options**

## POST

`/login`
email, password

## With token
### client
## GET

`/client`
**get account**

`/client/appointments`
**get appointment list**

`/client/appointment`
*request: id*
**get appointment details**

`/client/docs/debts`
**get debt list**

`/client/docs/debt`
*request: id*
**get debt details**

`/client/docs/forms`
**get form list**

`/client/docs/form`
*request: id*
**get form details**

`/client/docs/debtors`
**get debtor document list**

`/client/docs/debtor`
*request: id*
**get debtor document details**

`/client/docs/others`
**get other document list**

`/client/docs/other`
*request: id*
**get other document details**

`/client/docs/debts/search`
*request: search*
**search debt list**

`/client/docs/debtors/search`
*request: search*
**search debtor document list**

`/client/docs/others/search`
*request: search*
**search other document list**

## POST
`/client/sign`
*request: document_id, author, signature (image file)*
**upload signature**

### consultant
## GET
`/consultant/company`
*request: id*
**get company details**

`/consultant/companies/all`
**get all company list**

`/consultant/employers`
**get company employer list**

`/consultant/companies`
**get company non-employer list (debt collector, debtor, enz)**

`/consultant/clients`
**get client list**

`/consultant/client`
*request: client_id*
**get client details**

`/consultant/client/delete-child`
*request: id (child ID)*
**delete client child**

`/consultant/client/debts`
*request: client_id*
**get client debt list**

`/consultant/client/debt/details`
*request: id, client_id (optional, display all consultant's client debts)*
**get client debt details**

`/consultant/client/debts/search`
*request: client_id, search*
**get client debt details**

`/consultant/client/incomes`
*request: client_id*
**get client income list**

`/consultant/client/outcomes`
*request: client_id*
**get client outcome/cost list**

`/consultant/appointments`
**get appointments**

`/consultant/appointment`
*request: id*
**get appointment details**

`/consultant/doc/forms`
`/consultant/doc/form`
`/consultant/doc/debtors`
`/consultant/doc/debtor`
`/consultant/doc/debtor-search`
`/consultant/doc/others`
`/consultant/doc/other`
`/consultant/doc/other-search`

`/consultant/client/debt/next-steps`
*request: client_id, debt_id*
**get client next debt statusses/steps**

`/consultant/client/debt/next-step`
*request: client_id, debt_id, status_id*
**change client's debt to next status**

`/consultant/client/templates`
*request: client_id, type (form, debtor, other)*
**get template list of specific document type**

## POST
`/consultant/doc/add`
*request: client_id, debt_id, title, option ('slug'), file, 
**upload other document**

`/consultant/client/create`
*request: email, password, confirm_password, gender, initial, firstname, lastname, card_id, birth_date, phonenumber, address, place_id*
**create client (only required fields)**

`/consultant/client/create-complete`
*request: id (by update), email, password, confirm_password, gender, initial, firstname, lastname, card_id, birth_date, phonenumber, address, social_security_id, birth_place, nationality, id_type, id_card_number, marital_status, partnership_reg, address, postal_code, place_id, bank_account, employer_id, authorized_date, partner_social_security_id, partner_initial, partner_firstname, partner_lastname, partner_gender, partner_birth_date, partner_birth_place, partner_nationality, partner_id_type, partner_id_card_number, children[fullname, birth_date, id (by update)]*
**create/update client details (complete fields)**

`/consultant/client/debt/create`
*request: client_id, reference_id, debtor_id, due_date, preference, terms, percentage, debt_amount, total_redeemed, redeem_per_month, total_redemption, notes*
**create debt for a client**

`/consultant/client/debt/update`
*request: id, client_id, reference_id, debtor_id, due_date, preference, terms, percentage, debt_amount, total_redeemed, redeem_per_month, total_redemption, notes*
**update debt for a client**

`/consultant/make-appointment`
*request: date, time, client_id, location_id, title, notes*
**make appointment**

`/consultant/company/manage`
*request: id (for update), name, address, postal_code, place_id, phone, email, bank_account, type (/company-types, array id's)*
**create/update company**

`/consultant/client/next-step`
*request: client_id*
**get next client step/status**

`/consultant/client/income/update`
*request: id (for update), client_id, client_type (client/partner), employer_id, amount, income_id*
**create/update client income**

`/consultant/client/outcome/update`
*request: id (for update), client_id, client_type (client/partner), company_id, client_number, amount, outcome_id*
**create/update client income**

`/consultant/sign`
*request: document_id, client_id, author, signature (image file)*
**upload signature**

### download
## GET
`/document/file-download`
*request: document_id, client_id (for consultant)*
**File download**

`/document/signatures`
*request: document_id*
**Check who has to sign the doc**

`/document/html-preview`
*request: document_id, client_id (for consultant)*
**HTML form preview**

`/document/pdf-download`
*request: document_id, client_id (for consultant)*
**Pdf form preview**

