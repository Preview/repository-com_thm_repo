CREATE TABLE #__repo_folder (
	id int(11) PRIMARY KEY,
	parent_id int(11) NULL REFERENCES #__repo_folder(id),
	name varchar(25) NOT NULL,
	description varchar(255),
	created Timestamp,
	modified Timestamp,
	modified_by int(11) UNSIGNED NOT NULL REFERENCES #__users(id),
	create_by int(11) UNSIGNED NOT NULL  REFERENCES #__users(id),
	viewlevels int(11) UNSIGNED NOT NULL  REFERENCES #__viewlevels(id),
);

CREATE TABLE #__repo_entity(
	id int(11) PRIMARY KEY,
	parent_id int(11) NOT NULL REFERENCES #__repo_folder(id),
	name varchar(25) NOT NULL,
	created Timestamp,
	modified Timestamp,
	modified_by int(11) UNSIGNED NOT NULL REFERENCES #__users(id),
	create_by int(11) UNSIGNED NOT NULL REFERENCES #__users(id),
	viewlevels int(11) UNSIGNED NOT NULL REFERENCES #__viewlevels(id),
);

CREATE TABLE #__repo_file(
	id int(11) PRIMARY KEY REFERENCES #__repo_entity(id),
	path varchar(100) NOT NULL,
	size long,
	minetype varchar(15),
);

CREATE TABLE #__repo_version(
	id int(11) NOT NULL REFERENCES #__repo_file(id),
	versionnumber int(11) NOT NULL,
	name varchar(25) NOT NULL, 
	modified timestamp,
	path varchar(100) NOT NULL,
	size long NOT NULL,
	mineTape varchar(15) NOT NULL,
	PRIMARY KEY(id, versionnumber)
);

CREATE TABLE #__repo_link(
	id int(11) PRIMARY KEY REFERENCES #__repo_entity(id),
	link varchar(250) NOT NULL,
);
