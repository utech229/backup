curl --request DELETE \
  "https://www.googleapis.com/drive/v3/files/$1?key=$2" \
  --header "Authorization: Bearer $3" \
  --header "Accept: application/json" \
  --compressed
