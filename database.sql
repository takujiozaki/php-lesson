#DB定義
create database test default charset utf8;

#TABLE定義
create table messages (
    id int primary key auto_increment,
    user_name varchar(100),
    user_email varchar(100),
    main TEXT
);

#初期データ投入
insert into messages(user_name, user_email, main) values('tkj','takujiozaki@gmail.com','投稿テスト１');

#アクセス権設定
grant all on test.* to 'test'@'%' identified by 'test';