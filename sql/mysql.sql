CREATE TABLE `mod_dtransport_alerts` (
`id_alert` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL DEFAULT '0',
  `limit` smallint(6) NOT NULL DEFAULT '0',
  `mode` tinyint(1) NOT NULL DEFAULT '0',
  `lastactivity` int(10) NOT NULL DEFAULT '0',
  `alerted` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_categories` (
`id_cat` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `nameid` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_catitem` (
  `cat` int(11) NOT NULL,
  `soft` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_downs` (
`id_down` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL,
  `downs` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL,
  `date` int(10) NOT NULL,
  `id_file` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_features` (
`id_feat` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `created` int(10) NOT NULL,
  `modified` int(10) NOT NULL DEFAULT '0',
  `nameid` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_files` (
`id_file` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL,
  `remote` tinyint(1) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `group` int(11) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL,
  `mime` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_groups` (
`id_group` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `id_soft` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_items` (
`id_soft` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `version` varchar(50) NOT NULL,
  `shortdesc` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `limits` smallint(6) NOT NULL DEFAULT '0',
  `created` int(10) NOT NULL DEFAULT '0',
  `modified` int(10) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `secure` tinyint(1) NOT NULL DEFAULT '0',
  `groups` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `rating` decimal(11,1) NOT NULL DEFAULT '0.0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `nameid` varchar(150) NOT NULL,
  `daily` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `siterate` smallint(6) NOT NULL DEFAULT '0',
  `screens` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `langs` varchar(255) NOT NULL,
  `author_name` varchar(50) NOT NULL,
  `author_url` varchar(255) NOT NULL,
  `author_email` varchar(100) NOT NULL,
  `author_contact` tinyint(1) NOT NULL DEFAULT '1',
  `password` varchar(50) NOT NULL,
  `deletion` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(15) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_edited` (
  `id_item` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL DEFAULT '0',
  `type` varchar(5) NOT NULL DEFAULT 'new',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `fields` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_itemtag` (
  `id_soft` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_licences` (
`id_lic` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `nameid` varchar(150) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_licsoft` (
  `id_lic` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_logs` (
`id_log` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `log` text NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_meta` (
`id_meta` int(11) NOT NULL,
  `id_element` int(11) NOT NULL DEFAULT '0',
  `type` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_platforms` (
`id_platform` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `nameid` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_platsoft` (
  `id_platform` int(11) NOT NULL,
  `id_soft` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_screens` (
`id_screen` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `id_soft` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_tags` (
`id_tag` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `tagid` varchar(50) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_dtransport_votedata` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  `id_soft` int(11) NOT NULL,
  `rate` decimal(10,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mod_dtransport_alerts`
 ADD PRIMARY KEY (`id_alert`), ADD KEY `id_soft` (`id_soft`);

ALTER TABLE `mod_dtransport_categories`
 ADD PRIMARY KEY (`id_cat`);

ALTER TABLE `mod_dtransport_catitem`
 ADD KEY `cat` (`cat`), ADD KEY `soft` (`soft`);

ALTER TABLE `mod_dtransport_downs`
 ADD PRIMARY KEY (`id_down`), ADD KEY `uid` (`uid`,`id_soft`), ADD KEY `ip` (`ip`);

ALTER TABLE `mod_dtransport_features`
 ADD PRIMARY KEY (`id_feat`);

ALTER TABLE `mod_dtransport_files`
 ADD PRIMARY KEY (`id_file`), ADD KEY `id_soft` (`id_soft`);

ALTER TABLE `mod_dtransport_groups`
 ADD PRIMARY KEY (`id_group`);

ALTER TABLE `mod_dtransport_items`
 ADD PRIMARY KEY (`id_soft`);

ALTER TABLE `mod_dtransport_itemtag`
 ADD KEY `id_soft` (`id_soft`,`id_tag`);

ALTER TABLE `mod_dtransport_licences`
 ADD PRIMARY KEY (`id_lic`), ADD KEY `nameid` (`nameid`);

ALTER TABLE `mod_dtransport_licsoft`
 ADD KEY `id_lic` (`id_lic`,`id_soft`);

ALTER TABLE `mod_dtransport_logs`
 ADD PRIMARY KEY (`id_log`), ADD KEY `id_soft` (`id_soft`);

ALTER TABLE `mod_dtransport_meta`
 ADD PRIMARY KEY (`id_meta`);

ALTER TABLE `mod_dtransport_platforms`
 ADD PRIMARY KEY (`id_platform`);

ALTER TABLE `mod_dtransport_platsoft`
 ADD KEY `id_platform` (`id_platform`,`id_soft`);

ALTER TABLE `mod_dtransport_screens`
 ADD PRIMARY KEY (`id_screen`);

ALTER TABLE `mod_dtransport_tags`
 ADD PRIMARY KEY (`id_tag`);

ALTER TABLE `mod_dtransport_votedata`
 ADD KEY `id_soft` (`id_soft`), ADD KEY `uid` (`uid`), ADD KEY `ip` (`ip`);

ALTER TABLE `mod_dtransport_alerts`
MODIFY `id_alert` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_categories`
MODIFY `id_cat` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_downs`
MODIFY `id_down` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_features`
MODIFY `id_feat` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_files`
MODIFY `id_file` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_groups`
MODIFY `id_group` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_items`
MODIFY `id_soft` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_licences`
MODIFY `id_lic` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_logs`
MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_meta`
MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_platforms`
MODIFY `id_platform` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_screens`
MODIFY `id_screen` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mod_dtransport_tags`
MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mod_dtransport_edited`
  ADD PRIMARY KEY (`id_item`),
  ADD UNIQUE KEY `id_soft` (`id_soft`);

ALTER TABLE `mod_dtransport_edited`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;
