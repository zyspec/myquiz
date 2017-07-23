
CREATE TABLE myquiz_admin (
  quizzID int(11) NOT NULL auto_increment,
  quizzTitle varchar(255) NOT NULL default '',
  timeStamp int(11) NOT NULL default '0',
  voters mediumint(9) NOT NULL default '0',
  nbscore tinyint(9) NOT NULL default '10',
  displayscore tinyint(1) NOT NULL default '0',
  displayresults tinyint(1) NOT NULL default '0',
  tektek tinyint(1) NOT NULL default '0',
  comment text,
  active tinyint(1) NOT NULL default '1',
  restrict_user tinyint(1) NOT NULL default '1',
  log_user tinyint(1) NOT NULL default '1',
  image varchar(50) default NULL,
  cid int(11) NOT NULL default '1',
  contrib tinyint(1) NOT NULL default '1',
  expire varchar(16) NOT NULL default 'xx-xx-xxxx xx:xx',
  emailadmin tinyint(1) NOT NULL default '0',
  admemail varchar(50) default NULL,
  administrator varchar(50) default NULL,
  conditions varchar(50) NOT NULL default '0',
  PRIMARY KEY  (quizzID),
  KEY quizzID (quizzID)
) ENGINE=MyISAM;

CREATE TABLE myquiz_categories (
  cid int(9) NOT NULL auto_increment,
  ustid int(9) NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  comment varchar(255) default NULL,
  image varchar(50) default NULL,
  PRIMARY KEY  (cid)
) ENGINE=MyISAM;

CREATE TABLE myquiz_check (
  ip varchar(20) default NULL,
  time varchar(14) NOT NULL default '',
  username varchar(50) default NULL,
  email varchar(50) default NULL,
  qid int(11) NOT NULL default '0',
  score tinyint(2) NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  KEY qid (qid)
) ENGINE=MyISAM;

CREATE TABLE myquiz_data (
  pollID int(11) NOT NULL default '0',
  optionText char(255) NOT NULL default '',
  optionCount int(11) NOT NULL default '0',
  voteID int(11) NOT NULL default '0'
) ENGINE=MyISAM;

CREATE TABLE myquiz_datacontrib (
  pollID int(11) NOT NULL default '0',
  optionText char(255) NOT NULL default '',
  optionCount int(11) NOT NULL default '0',
  voteID int(11) NOT NULL default '0'
) ENGINE=MyISAM;

CREATE TABLE myquiz_desc (
  pollID int(11) NOT NULL auto_increment,
  pollTitle varchar(255) NOT NULL default '',
  timeStamp int(11) NOT NULL default '0',
  voters mediumint(9) NOT NULL default '0',
  qid tinyint(9) NOT NULL default '0',
  answer varchar(30) NOT NULL default '0',
  coef tinyint(3) NOT NULL default '1',
  good text,
  bad text,
  comment text,
  image varchar(255) default NULL,
  PRIMARY KEY  (pollID)
) ENGINE=MyISAM;

CREATE TABLE myquiz_descontrib (
  pollID int(11) NOT NULL auto_increment,
  pollTitle varchar(255) NOT NULL default '',
  timeStamp int(11) NOT NULL default '0',
  voters mediumint(9) NOT NULL default '0',
  qid tinyint(9) NOT NULL default '0',
  answer varchar(30) NOT NULL default '0',
  coef tinyint(3) NOT NULL default '1',
  good text,
  bad text,
  comment text,
  image varchar(255) default NULL,
  PRIMARY KEY  (pollID)
) ENGINE=MyISAM;