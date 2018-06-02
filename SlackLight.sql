DROP DATABASE IF EXISTS clk_slacklight;
CREATE DATABASE clk_slacklight;
USE clk_slacklight;


CREATE TABLE books (

	id int(11) NOT NULL AUTO_INCREMENT,

	categoryId int(11) NOT NULL,

	title varchar(255) NOT NULL,

	author varchar(255) NOT NULL,

	isbn varchar(255) NOT NULL,

	price decimal(10,2) NOT NULL,

	PRIMARY KEY (id),

	KEY categoryId (categoryId)

) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;



CREATE TABLE categories (
	id int(11) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,

	PRIMARY KEY (id)

) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;



CREATE TABLE orderedbooks (
	id int(11) NOT NULL AUTO_INCREMENT,

	orderId int(11) NOT NULL,

	bookId int(11) NOT NULL,

	PRIMARY KEY (id),
	KEY orderId (orderId),
	KEY bookId (bookId)

) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;



CREATE TABLE orders (
	id int(11) NOT NULL AUTO_INCREMENT,
	userId int(11) NOT NULL,
	creditCardNumber char(16) NOT NULL,
	creditCardHolder varchar(255) NOT NULL,
	PRIMARY KEY (id),
	KEY userId (userId)

) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;




CREATE TABLE userMessageRef (
	userId int(11) NOT NULL,
	messageId int(11) NOT NULL,
	marked int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (userId, messageId)
);

/*
CREATE TABLE channelMessageRef (
	channelId int(11) NOT NULL,
	messageId int(11) NOT NULL,
	PRIMARY KEY (channelId, messageId)
);
*/

CREATE TABLE messages (
	id int(11) NOT NULL AUTO_INCREMENT,
	authorId int(11) NOT NULL,
	channelId int(11) NOT NULL,
	text varchar(2000) NOT NULL,
	creationTime DATETIME DEFAULT CURRENT_TIMESTAMP,
	edited int(1) NOT NULL DEFAULT '0',
	deleted int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;

CREATE TABLE channelUserRef (
  channelId int(11) NOT NULL,
  userId int(11) NOT NULL,
  marked int(1) NOT NULL DEFAULT '0',
  lastRead int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (channelId, userId)
);

CREATE TABLE channels (
	id int(11) NOT NULL AUTO_INCREMENT,
	name char(16) NOT NULL,
	description varchar(255) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY name (name)

) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;


CREATE TABLE users (
	id int(11) NOT NULL AUTO_INCREMENT,
	userName varchar(255) NOT NULL,
	passwordHash char(40) NOT NULL,
	lastreadmsg int(11) NOT NULL DEFAULT '0',

	PRIMARY KEY (id),
	UNIQUE KEY userName (userName)

) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;

ALTER TABLE channelUserRef
ADD CONSTRAINT channeluserref_1 FOREIGN KEY (channelId) REFERENCES channels (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE channelUserRef
ADD CONSTRAINT channeluserref_2 FOREIGN KEY (userId) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE messages
ADD CONSTRAINT messages_1 FOREIGN KEY (authorId) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE messages
ADD CONSTRAINT messages_2 FOREIGN KEY (channelId) REFERENCES channels (id) ON DELETE CASCADE ON UPDATE CASCADE;

/*
ALTER TABLE channelMessageRef
ADD CONSTRAINT channelMessages_1 FOREIGN KEY (channelId) REFERENCES channels (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE channelMessageRef
ADD CONSTRAINT channelMessages_2 FOREIGN KEY (messageId) REFERENCES messages (id) ON DELETE CASCADE ON UPDATE CASCADE;
*/

ALTER TABLE userMessageRef
ADD CONSTRAINT userMessages_1 FOREIGN KEY (userId) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE userMessageRef
ADD CONSTRAINT userMessages_2 FOREIGN KEY (messageId) REFERENCES messages (id) ON DELETE CASCADE ON UPDATE CASCADE;








ALTER TABLE books
ADD CONSTRAINT books_ibfk_1 FOREIGN KEY (categoryId) REFERENCES categories (id) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE orderedbooks
ADD CONSTRAINT orderedbooks_ibfk_2 FOREIGN KEY (bookId) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,

ADD CONSTRAINT orderedBooks_ibfk_1 FOREIGN KEY (orderid) REFERENCES orders (id) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE orders
ADD CONSTRAINT orders_ibfk_1 FOREIGN KEY (userId) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO categories VALUES (1, 'Mobile & Wireless Computing');
INSERT INTO categories VALUES (2, 'Functional Programming');
INSERT INTO categories VALUES (3, 'C / C++');
INSERT INTO categories VALUES (4, '<< New Publications >>');

INSERT INTO books VALUES (1, 1, 'Hello, Android: Introducing Google''s Mobile Development Platform', 'Ed Burnette', '9781934356562', 19.97);
INSERT INTO books VALUES (2, 1, 'Android Wireless Application Development', 'Shane Conder, Lauren Darcey', '0321743016', 31.22);
INSERT INTO books VALUES (5, 1, 'Professional Flash Mobile Development', 'Richard Wagner', '0470620072', 19.90);
INSERT INTO books VALUES (7, 1, 'Mobile Web Design For Dummies', 'Janine Warner, David LaFontaine', '9780470560969', 16.32);
INSERT INTO books VALUES (11, 2, 'Introduction to Functional Programming using Haskell', 'Richard Bird', '9780134843469', 74.75);
INSERT INTO books VALUES (12, 2, 'Scripting (Attacks) for Beginners - <script type="text/javascript">alert(''All your base are belong to us!'');</script>', 'John Doe', '1234567890', 9.99);
INSERT INTO books VALUES (14, 2, 'Expert F# (Expert''s Voice in .NET)', 'Antonio Cisternino, Adam Granicz, Don Syme', '9781590598504', 47.64);
INSERT INTO books VALUES (16, 3, 'C Programming Language (2nd Edition)', 'Brian W. Kernighan, Dennis M. Ritchie', '0131103628', 48.36);
INSERT INTO books VALUES (27, 3, 'C++ Primer Plus (5th Edition)', 'Stephan Prata', ' 9780672326974', 36.94);
INSERT INTO books VALUES (29, 3, 'The C++ Programming Language', 'Bjarne Stroustrup', '0201700735', 67.49);




/* insert dummy users */
INSERT INTO users VALUES (1, 'scm4', 'a8af855d47d091f0376664fe588207f334cdad22', 0);
INSERT INTO users VALUES (2, 'clk', '216af29551d5577aaf1c40b32677df4d77211c59', 0);

/* insert dummy channels and connect to users */
INSERT INTO channels VALUES (1, "social-newsboard", "Learn everything about the latest news and gossip inside the company.");
INSERT INTO channels VALUES (2, "announcements", "News and updates about the company. Read only.");
INSERT INTO channels VALUES (3, "announcements-hr", "News from people management. Who joins, who proceeds, who leaves");

INSERT INTO channelUserRef VALUES (1, 1, 0, 0);
INSERT INTO channelUserRef VALUES (2, 1, 1, 0);
INSERT INTO channelUserRef VALUES (1, 2, 0, 0);
INSERT INTO channelUserRef VALUES (2, 2, 0, 0);
INSERT INTO channelUserRef VALUES (3, 2, 0, 0);

/* insert dummy messages */
INSERT INTO messages VALUES (1, 1, 2, "here is the first message in announcements", CURRENT_TIMESTAMP(), 0, 0);
INSERT INTO messages VALUES (2, 1, 2, "here is the second message in announcements (unread)", CURRENT_TIMESTAMP(), 1, 0);
INSERT INTO messages VALUES (3, 2, 2, "here is the thrid message in announcements", CURRENT_TIMESTAMP(), 0, 0);
INSERT INTO messages VALUES (4, 1, 2, "here is the fourth message in announcements (unread)", CURRENT_TIMESTAMP(), 1, 0);
INSERT INTO messages VALUES (5, 1, 1, "here is the first message in soc-news", CURRENT_TIMESTAMP(), 0, 0);
INSERT INTO messages VALUES (6, 1, 1, "here is the second message in soc-news (unread)", CURRENT_TIMESTAMP(), 1, 0);
INSERT INTO messages VALUES (7, 2, 3, "here is the first message in announcements-hr", CURRENT_TIMESTAMP(), 0, 0);

INSERT INTO userMessageRef VALUES (1, 1, 0);
INSERT INTO userMessageRef VALUES (1, 2, 1);
INSERT INTO userMessageRef VALUES (1, 3, 0);
INSERT INTO userMessageRef VALUES (1, 4, 1);
INSERT INTO userMessageRef VALUES (1, 5, 0);
INSERT INTO userMessageRef VALUES (1, 6, 1);
INSERT INTO userMessageRef VALUES (2, 7, 0);

/*
created DATETIME DEFAULT CURRENT_TIMESTAMP,
'2018-05-06 16:41:23'
*/