CREATE TABLE #__thm_repo_folder (
	id int(10) UNSIGNED AUTO_INCREMENT,
	parent_id int(10) UNSIGNED NULL,
	name varchar(25) NOT NULL,
	description varchar(255),
	created Timestamp,
	modified Timestamp,
	modified_by int(11) NOT NULL,
	create_by int(11) NOT NULL,
	viewlevels int(10) UNSIGNED NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(parent_id) REFERENCES #__thm_repo_folder(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(modified_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(create_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(viewlevels) REFERENCES #__viewlevels(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_entity (
	id int(10) UNSIGNED AUTO_INCREMENT,
	parent_id int(10) UNSIGNED NOT NULL,
	name varchar(25) NOT NULL,
	description varchar(255),
	created Timestamp,
	modified Timestamp,
	modified_by int(11) NOT NULL,
	create_by int(11) NOT NULL,
	viewlevels int(10) UNSIGNED NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(parent_id) REFERENCES #__thm_repo_folder(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(modified_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(create_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(viewlevels) REFERENCES #__viewlevels(id) ON UPDATE CASCADE ON DELETE RESTRICT

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_file (
	id int(10) UNSIGNED,
	path varchar(100) NOT NULL,
	size long,
	minetype varchar(15),
	PRIMARY KEY(id),
	FOREIGN KEY(id) REFERENCES #__thm_repo_entity(id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_version (
	id int(10) UNSIGNED NOT NULL,
	versionnumber int(10) UNSIGNED NOT NULL,
	name varchar(25) NOT NULL, 
	modified timestamp,
	path varchar(100) NOT NULL,
	size long NOT NULL,
	mineTape varchar(15) NOT NULL,
	PRIMARY KEY(id, versionnumber),
	FOREIGN KEY(id) REFERENCES #__thm_repo_file(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_link (
	id int(10) UNSIGNED ,
	link varchar(250) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(id) REFERENCES #__thm_repo_entity(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
