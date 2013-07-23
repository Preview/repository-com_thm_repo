START TRANSACTION;

CREATE TABLE #__thm_repo_folder (
	id int(10) UNSIGNED AUTO_INCREMENT,
	lft int(12) UNSIGNED NULL,
	rgt int(12) UNSIGNED NULL,
	parent_id int(10) UNSIGNED NULL,
	name varchar(25) NOT NULL,
	description varchar(255) NULL,
	created timestamp NOT NULL,
	modified timestamp NOT NULL,
	modified_by int(11) NOT NULL,
	created_by int(11) NOT NULL,
	viewlevel int(10) UNSIGNED NOT NULL,
	PRIMARY KEY(id),
	KEY thm_repo_folder_lft (lft),
	KEY thm_repo_folder_rgt (rgt),
	FOREIGN KEY(parent_id) REFERENCES #__thm_repo_folder(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(modified_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(created_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(viewlevel) REFERENCES #__viewlevels(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_entity (
	id int (10) UNSIGNED,
	parent_id int(10) UNSIGNED NOT NULL,
	ordering int(12),
	PRIMARY KEY(id),
	FOREIGN KEY(parent_id) REFERENCES #__thm_repo_folder(id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE #__thm_repo_file (
	id int(10) UNSIGNED,
	current_version int(10) UNSIGNED NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(id) REFERENCES #__thm_repo_entity(id) ON UPDATE CASCADE ON DELETE CASCADE
 	-- , FOREIGN KEY(current_version) REFERENCES #__thm_repo_version(version) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_version (
	id int(10) UNSIGNED AUTO_INCREMENT,
	version int (10) UNSIGNED NOT NULL DEFAULT 1,
	name varchar(25) NOT NULL,
	description varchar(255),
	created timestamp,
	modified timestamp,
	modified_by int(11) NOT NULL,
	created_by int(11) NOT NULL,
	viewlevel int(10) UNSIGNED NOT NULL,
	path varchar(100) NOT NULL,
	size long NOT NULL,
	mimetype varchar(100) NOT NULL,
	PRIMARY KEY(id, version),
	FOREIGN KEY(id) REFERENCES #__thm_repo_file(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(modified_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(created_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(viewlevel) REFERENCES #__viewlevels(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__thm_repo_link (
	id int(10) UNSIGNED AUTO_INCREMENT,
	name varchar(25) NOT NULL,
	description varchar(255),
	created Timestamp NOT NULL,
	modified Timestamp NOT NULL,
	modified_by int(11) NOT NULL,
	created_by int(11) NOT NULL,
	viewlevel int(10) UNSIGNED NOT NULL,
	link varchar(250) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(id) REFERENCES #__thm_repo_entity(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(modified_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(created_by) REFERENCES #__users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
	FOREIGN KEY(viewlevel) REFERENCES #__viewlevels(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;
