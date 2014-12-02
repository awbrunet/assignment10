CREATE TABLE IF NOT EXISTS tblUser (pmkUserId int(11) NOT NULL AUTO_INCREMENT, fldFName varchar(20) DEFAULT NULL, 
fldLName varchar(20) DEFAULT NULL, 
fldEmail varchar(65) DEFAULT NULL, 
fldLogStatus int(11) NOT NULL DEFAULT "0", 
fldAllergy varchar(20) DEFAULT NULL, 
fldAdmin int(11) NOT NULL DEFAULT "0", 
PRIMARY KEY (pmkUserId) ) 
ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS tblSavedRestaurants (fnkUserId int(11) NOT NULL, 
fnkRestId int(11) NOT NULL, 
PRIMARY KEY (fnkUserId, fnkRestId) ) 
ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS tblRestaurants (pmkRestId int(11) NOT NULL AUTO_INCREMENT, 
fldRestName varchar(50) DEFAULT NULL, 
fldFoodType varchar(20) DEFAULT NULL, 
fldMenuType varchar(30) DEFAULT NULL, 
fldStreetAdd varchar(50) DEFAULT NULL, 
fldCity varchar(20) DEFAULT NULL, 
fldState varchar(20) DEFAULT NULL, 
fldZip varchar(10) DEFAULT NULL, 
fldPhone varchar(15) DEFAULT NULL, 
fldURL varchar(100) DEFAULT NULL, 
PRIMARY KEY (pmkRestId) ) 
ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS tblSubmittedRestaurants (fnkUserId int(11) NOT NULL, 
fnkRestId int(11) NOT NULL, 
PRIMARY KEY (fnkUserId, fnkRestId) ) 
ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
  