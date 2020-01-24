///////////////////////////////////////////////////////////////
							LOGIN
//////////////////////////////////////////////////////////////

URL : localhost/chiefsRS/api/web/users/login

Headers : 
1) Content-Type : application/json

Request Params :

{"user_email":"jay.varan@zenocraft.com","password":"jay","device_id":"APA91bH5NJVttJXKC04OA36UDykn8RUIwtbciU6Y9t0Yg5GikL7RLz1aUIagVb9d8kPJJxqVOcCe8o9Ai67xrrXRDonlHl_R7id-p-uKbl8Gkr6NsehbqE8"}

Response Params :

{"success":1,"message":"successfully login.","data":{"user_email":"jay.varan@zenocraft.com","user_id":3,"first_name":"jay","last_name":"varan","address":"jayvaran@zenocraft","device_token":"APA91bH5NJVttJXKC04OA36UDykn8RUIwtbciU6Y9t0Yg5GikL7RLz1aUIagVb9d8kPJJxqVOcCe8o9Ai67xrrXRDonlHl_R7id-p-uKbl8Gkr6NsehbqE8","gcm_registration_id":"","auth_token":"74de7262d7d9d00a907647d4b8a5891f"}}

//////////////////////////////////////////////////////////////////////////////////
								CHANGE PASSWORD
//////////////////////////////////////////////////////////////////////////////////

URL : localhost/chiefsRS/api/web/users/change-password

Request Params :

{"old_password":"admin","new_password":"admin123","user_id":"16"}

Response Params :

{"success":1,"message":"Your password has been changed successfully.","data":{"user_id":3,"user_email":"jay.varan@zenocraft.com"}}

//////////////////////////////////////////////////////////////////////////////////
								FORGOT PASSWORD
//////////////////////////////////////////////////////////////////////////////////

URL : localhost/chiefsRS/api/web/users/forgot-password

Request Params :

{"user_email":"jay.varan@zenocraft.com"}

Response Params :

{"success":1,"message":"Email has been sent successfully please check your email. ","data":{"user_email":"jay.varan@zenocraft.com"}}

//////////////////////////////////////////////////////
				EDIT USER PROFILE
//////////////////////////////////////////////////////

URL : localhost/chiefsRS/api/web/users/edit-profile

Headers : 
1) Content-Type : application/json
2) auth_token   : b4ceb860cb64401ac7f44b946515f485

Request Params :

{"user_id":"3","first_name":"jay","last_name":"varan","email":"jay.varan@zenocraft.com","address":"jayvaran@zenocraft","contact_no":"8787878789"}

Response Params : 

{"success":1,"message":"Your profile has been updated successfully.","data":{"user_email":"jay.varan@zenocraft.com","user_id":3,"first_name":"jay","last_name":"varan","address":"jayvaran@zenocraft","contact_no":"8787878789","auth_token":"b4ceb860cb64401ac7f44b946515f485"}}

/////////////////////////////////////////////////////////////////////////////////
				Get Reservation List by manager(user_id)
/////////////////////////////////////////////////////////////////////////////////

URL : localhost/chiefsRS/api/web/users/get-reservation-list

Headers:
1) Content-Type : application/json
2) auth_token   : b4ceb860cb64401ac7f44b946515f485

Request Params :

{"user_id":"3"}

Response Params :

{"success":1,"message":"User Reservations Details.","data":$data}

////////////////////////////////////////////////////////////////////////////////////
								Delete Floor API
////////////////////////////////////////////////////////////////////////////////////


URL : http://121.55.237.213:8012/chiefsRS/api/web/floor/delete-floor
Request Params : 
{"user_id":"3","floor_id":"42"}
RESPONSE PARAMS :
{"success":200,"message":"Floor is deleted successfully.","data":[]}

///////////////////////////////////////////////////////////////////////////////////
								Delete Table API
///////////////////////////////////////////////////////////////////////////////////

URL : http://121.55.237.213:8012/chiefsRS/api/web/floor/delete-table
Request Params :
{"user_id":"3","table_id":"128"}
RESPONSE PARAMS :
{"success":200,"message":"Table is deleted successfully.","data":[]}