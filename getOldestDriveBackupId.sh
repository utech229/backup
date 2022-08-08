curl \
  "https://www.googleapis.com/drive/v3/files?orderBy=createdTime&q=%271kgXAfkBtm3Nf39UoRn6EF8WXhFdEPRsa%27%20in%20parents%20and%20trashed%3Dfalse%20and%20name%20contains%20%27$1%27&key=$2" \
  --header "Authorization: Bearer $3" \
  --header "Accept: application/json" \
  --compressed
