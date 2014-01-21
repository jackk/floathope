CREATE TABLE `charity` (
  `charityid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `ein` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`charityid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `donation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `charityid` int(11) DEFAULT NULL,
  `confirmationid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification` varchar(7) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `cost_per_charge` float NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `recordedvisit` (
  `id` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `siteid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `site` (
  `siteid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` text NOT NULL,
  PRIMARY KEY (`siteid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `user` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text NOT NULL,
  `max_charge_dollars` int(11) NOT NULL,
  `charge_per_visit_cents` float NOT NULL,
  `password` text NOT NULL,
  `charityid` int(11) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `usersites` (
  `userid` int(11) unsigned NOT NULL,
  `siteid` int(11) DEFAULT NULL,
  KEY `siteid` (`siteid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;