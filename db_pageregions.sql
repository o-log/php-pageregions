array (
'create table olog_pageregion_block (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8  /* rand87643587 */;',
'alter table olog_pageregion_block add column is_published int not null default 0 /* rand87643587 */;',
'alter table olog_pageregion_block add column weight int not null default 0 /* rand87643587 */;',
'alter table olog_pageregion_block add column region varchar(100) not null default "" /* rand87643587 */;',
'alter table olog_pageregion_block add column pages text not null /* rand863456345 */;',
'alter table olog_pageregion_block add column cache int not null default 8 /* rand6752345 */;',
'alter table olog_pageregion_block add column body text not null /* rand6754235 */;',
'alter table olog_pageregion_block add column info varchar(255) not null default "" /* rand908734 */;',
'alter table olog_pageregion_block add column visible_only_for_administrators int not null default 0 /* rand7752345 */;',
'alter table olog_pageregion_block add column execute_pseudocode int not null default 0 /* rand0993453 */;',
'insert into olog_auth_permission (title) values ("PERMISSION_MODULENAME_MANAGE_NODES") /* 87673455 */;',
)