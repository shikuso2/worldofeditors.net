<?php

function is_dev()
{
	if (getenv("DEV"))
		return true;

	return false;
}

function is_prod()
{
	return !is_dev();
}

