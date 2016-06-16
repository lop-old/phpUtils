#!/bin/bash
clear

pushd public/ 1>/dev/null || exit 1

	php index.php update $@

popd 1>/dev/null

