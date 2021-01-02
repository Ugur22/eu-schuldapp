##GET

`/locations`
`/test`

##POST

###GENERAL

`/login`
*request: email, password*

###CLIENT SIDE

`/client`
*request: email, password*
**get account**

`/client/appointments`
*request: email, password*
**get appointment list**

`/client/appointment`
*request: email, password, id*
**get appointment details**

`/client/docs/debts`
*request: email, password*
**get own debt list**

`/client/docs/debt`
*request: email, password, id*
**get debt details**

`/client/docs/debts/search`
*request: email, password, search*
**search existed debt list, it will search in debtor's name, ref, status and notes**

`/consultant/client/debt/create`
*request: email (required), password (required), client_id (required), due_date (required), reference_id, debtor_id, preference, terms, debt_amount,total_redeemed, redeem_per_month, total_redemption, notes*
**create new debt**

`/consultant/client/debt/update`
*request: email (required), password (required), debt_id (required), due_date (required), reference_id, debtor_id, preference, terms, debt_amount,total_redeemed, redeem_per_month, total_redemption, notes*
**update debt**

`/client/docs/debtors`
*request: email, password*
**get document list of debtors**

`/client/docs/debtor`
*request: email, password, id*
**get document debtor's details**

`/client/docs/forms`
*request: email, password*
**get available forms**

`/client/docs/form`
*request: email, password, id*
**download form document**

`/client/docs/others`
*request: email, password*
**get other document list**

`/client/docs/other`
*request: email, password, id*
**download other document list**

`/client/docs/others/search`
*request: email, password, search*
**earching for other documents based on doc name/title**

`/client/sign`
*required: email, password, document_id, signature (signature image), author (client/partner)*

###CONSULTANT SIDE

`/consultant/clients`
*request: email, password*
**get client list**

`/consultant/client`
*request: email, password, id*
**get client details**

`/consultant/client/create`
*request: email, password, initial, firstname, lastname, card_id, gender, birth_date, address, place_id*
**create new client**

`/consultant/appointments`
*request: email, password*
**get appointment list**

`/consultant/appointment`
*request: email, password, id*
**get appointment details**

`/consultant/make-appointment`
*request: email, password, date, time, client_id, location_id, title, notes*
**make new appointment**

`/consultant/client/debts`
*request: email, password, client_id*
**get client's debts**

`/consultant/client/debt/details`
*request: email, password, client_id, id (debt id)*
**get client's debt details**

`/consultant/client/debts/search`
*request: email, password, client_id, search*
**searching client's debts**

`/consultant/doc/forms`
*request: email, password, client_id*
**get client's forms**

`/consultant/doc/form`
*request: email, password, client_id, id (form id)*
**get client's form details**

`/consultant/doc/debtors`
*request: email, password, client_id*
**get client's debtor forms**

`/consultant/doc/debtor`
*request: email, password, client_id, id (form id)*
**get client's debtor form details**

`/consultant/doc/debtor-search`
*request: email, password, client_id, search*
**get client's debtor forms search**

`/consultant/doc/others`
*request: email, password, client_id*
**get client's other forms**

`/consultant/doc/other`
*request: email, password, client_id, id (form id)*
**get client's other form details**

`/consultant/doc/other-search`
*request: email, password, client_id, search*
**get client's other forms search**

`/consultant/companies`
*request: email, password*
**get company debtor list**

`/consultant/employers`
*request: email, password*
**get company debtor list**

`/consultant/client/next`
*request: email, password, client_id*
**update client status/step to next status/step**

`/consultant/client/debt/next-steps`
*request: email, password, client_id, debt_id*
**list of next debt statuses/steps. Need to be selected for debt to go to next status/step**

`/consultant/client/debt/next-step`
*request: email, password, client_id, debt_status_id*
**update debt status**

`/consultant/client/templates`
*request: email, password, client_id*
**get pdf template list for specific client**

`/consultant/doc/add`
*required: email, password, client_id, title
optional: main (1 or none), file (if upload file), template_id (if pdf otherwise 0)*
**create/add document for client, could be file/picture or pdf from template.**

`/client/sign`
*required: email, password, document_id, signature (signature image), client_id, author (client/consultant/partner)*

###DOWNLOAD

`/document/html-preview`
*request: email, password, client_id (not required as client), document_id*
**HTML preview client's form**

`/document/pdf-download`
*request: email, password, client_id (not required as client), document_id*
**download/stream pdf client's pdf form**

`/document/file-download`
*request: email, password, client_id (not required as client), document_id*
**download/stream pdf client's pdf form**

`/document/check-signatures`
*request: email, password, document_id*
**Check if document complete signed, or who need to sign**