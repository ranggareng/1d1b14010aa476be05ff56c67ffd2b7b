CREATE TABLE public.m_users(
	id serial,
	email VARCHAR(45) NOT NULL,
	password VARCHAR(255) NOT NULL,
	PRIMARY KEY (id)
);

INSERT INTO m_users(email, password) VALUES ('mochamad.rangga@gmail.com', '$2y$10$m5I74OxhTwNHK8WGoLALwuFtMHGon6nuxxkUdsJjHnAXIdGnTqO4e');

CREATE TABLE public.access_tokens (
	id serial,
	user_id INTEGER NOT NULL,
	token VARCHAR(64) NOT NULL,
	expire_at TIMESTAMP NOT NULL,
	PRIMARY KEY (id),
	CONSTRAINT FK__m_users FOREIGN KEY (user_id) REFERENCES m_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE public.email_sent (
    id serial,
    user_id INTEGER NOT NULL,
    sent_at TIMESTAMP NOT NULL,
    content text,
    CONSTRAINT FK_email_m_users FOREIGN KEY (user_id) REFERENCES m_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION
);