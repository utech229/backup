curl -X POST -L \
 -H "Authorization: Bearer $1" \
 -F "metadata={ \
 name : '$2', \
 mimeType : 'application/gzip', \
 parents: ['1kgXAfkBtm3Nf39UoRn6EF8WXhFdEPRsa'] \
 };type=application/json;charset=UTF-8" \
 -F "file=@$2;type=application/gzip" \
 "https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart"
