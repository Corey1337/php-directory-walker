#!/bin/bash

sum=0
for targetfile in `find -name count -type f`; do
  number_only=$(tr -dc '0-9' < $targetfile)
  sum=$(($sum+$number_only))
done

echo $sum

