#!/bin/bash

for num in $(seq $1 $2)
do
	echo "Dia $num"
	php mercadopublico_v2.php $num
done
