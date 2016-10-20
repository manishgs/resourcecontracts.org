#!/bin/bash

export PGPASSWORD=$6
mkdir $5/$9
mkdir $5/${10}
mkdir $5/$7
IFS=$'\n'
for con in $(echo "$(psql -X -d $4 -U $3 -h $1 -p $2 -t -c "SELECT id,metadata->>'open_contracting_id' from contracts where metadata->'category'->>0='${11}' limit 10")"|xargs -n3)
  do
    contractid=$(echo $con|awk '{print $1}')
    ocid=$(echo $con|awk '{print $3}')
    psql -X -d $4 -U $3 -h $1 -p $2 -t -A -F"," -c "select text from contract_pages where contract_id=${contractid}" > $5/$9/${ocid}.txt
    sed -r 's/<br \/>//g' $5/$9/${ocid}.txt > $5/${10}/${ocid}.txt
done
zip -r -j $5/$8 $5/${10}
mv $5/$8'.zip' $5/$7/
size=$(wc -c $5/$7/$8'.zip' |awk '{print $1}')
filename=$8-${size}'.zip'
mv $5/$7/${8}'.zip' $5/$7/${filename}
