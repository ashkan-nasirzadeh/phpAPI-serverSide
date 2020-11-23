# phpApi-serverSide
this is the server part of phpApi and it's separated because of the convenience to use the server-side package with composer

to read the documentation please see [ashkan-nasirzadeh/phpApi-clientSide](https://github.com/ashkan-nasirzadeh/phpApi-clientSide)



composer require kia_nasirzadeh/phpapi_serverside

to do:

`where` should be like this:
where_and = [
'a' => '=#ali',
'b' => '!=#gholi',
'c' => '>#10',
'd' => 'LIKE#%asghar%'
]

where_or = [
'a' => '=#ali'
]

and `wherenot` and `like` should delete

add `min`, `max` date to readRowsPagination and readRows

in `addRow` we need `lastInsertedID` in answer

terminate JWT

make JWT just one time usage
