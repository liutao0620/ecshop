#创建一个数据库
create database bjshop;
#创建一个商品类型表
create table it_type(
	id tinyint unsigned  primary key auto_increment,
	type_name varchar(32) not null comment '商品类型的名称',
	index (type_name)
)engine myisam charset utf8;

#创建一个商品属性表
create table it_attribute(
	id smallint unsigned  primary key auto_increment,
	type_id tinyint unsigned  not null comment '商品类型表的id',
	attr_name varchar(32) not null comment '属性的名称',
	attr_type tinyint not null comment '属性的类型  0表示是唯一属性，1表示单选属性',
	attr_input_type tinyint not null comment '属性值的录入方式  0表示手工输入，1表示列表选择',
	attr_value varchar(64) not null default '' comment '存储属性列表选择中的值',
	index (type_id)
)engine myisam charset utf8;
#创建一个栏目表
create table it_category(
	id smallint unsigned  primary key auto_increment,
	cat_name varchar(32) not null comment '商品栏目名称',
	parent_id smallint  not null default 0 comment '父级栏目的id'
)engine myisam charset utf8;
#创建一个商品表
create table it_goods(
	id int unsigned  primary key auto_increment,
	goods_name varchar(32) not null comment '商品的名称',
	cat_id int  not null comment '商品所属栏目的id',
	goods_sn varchar(32) not null comment '商品的货号',
	market_price decimal(9,2) not null default 0.0  comment '商品的市场价格',
	shop_price decimal(9,2)  not null default 0.0 comment '本店价格',
	goods_ori varchar(128) not null default '' comment '原图的路径',
	goods_img varchar(128) not null default '' comment '中图的路径',
	goods_thumb varchar(128) not null default '' comment '小图的路径',
	is_best tinyint not null default 1  comment '是否是精品, 1表示是精品',
	is_hot tinyint not null default 1  comment '是否是热卖 ,1表示是热卖',
	is_new tinyint not null default 1  comment '是否是新品, 1表示是新品',
	is_sale tinyint not null default 1  comment '是否上架 ,1表示上架',
	is_delete tinyint not null default 0 comment '是否删除,1表示已经删除',
	add_time int not null default 0 comment '添加时间',
	goods_type tinyint unsigned not null default 0 comment '商品的类型id',
	goods_number smallint not null default 0 comment '商品的总库存',
	goods_desc varchar(256) not null default '' comment '商品的描述'
)engine myisam charset utf8;