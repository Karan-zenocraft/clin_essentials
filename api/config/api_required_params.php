<?php

$amRequiredParams                                             = [];
//REQUIRED PARAMETERS FOR USER CONTROLLER
$amRequiredParams["users"]["sign-up"]                         = ["firstname", "lastname", "username", "email", "password", "phone", "address_line_1", "city", "state", "zipcode", "birth_date"];
$amRequiredParams["users"]["verify-email"]                    = ["id", "verify", "email_verification_code"];
$amRequiredParams["users"]["mobile-verification"]             = ["activation_code"];
$amRequiredParams["users"]["verify-referral-code"]            = ["referral_code"];
$amRequiredParams["users"]["resend-verification-code"]        = ["phone"];
$amRequiredParams["users"]["login"]                           = ["email", "password"];
$amRequiredParams["users"]["change-password"]                 = ["old_password", "new_password", "auth_token"];
$amRequiredParams["users"]["forgot-password"]                 = ["email", "auth_token"];
$amRequiredParams["users"]["get-user-referral-code"]          = ["auth_token"];
$amRequiredParams["users"]["edit-profile"]                    = ["username", "city", "state", "zipcode", "address_line_1", "phone", "auth_token"];
$amRequiredParams["users"]["get-users-participation-counter"] = ["auth_token"];
$amRequiredParams["users"]["get-user-details"]                = ["auth_token"];
//$amRequiredParams["users"]["logout"]                          = [""];
$amRequiredParams["users"]["users-finincial-history"]         = ["auth_token", "filter_by", "page_no"];
$amRequiredParams["users"]["add-initial-deposit"]             = ["auth_token", "package_id", "first_time_bonus", "referral_bonus", "transaction_id"];
$amRequiredParams["users"]["manage-funds"]                    = ["auth_token", "transaction_type", "amount", "transaction_id"];
$amRequiredParams["users"]["reset-badge-count"]               = ["auth_token", "badge_count"];

return $amRequiredParams;
