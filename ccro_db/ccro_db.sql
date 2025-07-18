PGDMP      ;                }            ccro_db    17.3    17.3 a    �           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            �           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            �           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            �           1262    16389    ccro_db    DATABASE     m   CREATE DATABASE ccro_db WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en-US';
    DROP DATABASE ccro_db;
                     postgres    false            �           1247    16691 	   cert_type    TYPE     W   CREATE TYPE public.cert_type AS ENUM (
    'livebirth',
    'marriage',
    'death'
);
    DROP TYPE public.cert_type;
       public               postgres    false            �           1247    25022    payment_mode_enum    TYPE     J   CREATE TYPE public.payment_mode_enum AS ENUM (
    'cash',
    'gcash'
);
 $   DROP TYPE public.payment_mode_enum;
       public               postgres    false            �           1247    24990    transaction_status    TYPE     b   CREATE TYPE public.transaction_status AS ENUM (
    'pending',
    'approved',
    'cancelled'
);
 %   DROP TYPE public.transaction_status;
       public               postgres    false            �            1259    16659    admin    TABLE     �   CREATE TABLE public.admin (
    admin_id integer NOT NULL,
    auth_id integer,
    fullname text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    role text
);
    DROP TABLE public.admin;
       public         heap r       postgres    false            �            1259    16658    admin_admin_id_seq    SEQUENCE     �   CREATE SEQUENCE public.admin_admin_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 )   DROP SEQUENCE public.admin_admin_id_seq;
       public               postgres    false    234            �           0    0    admin_admin_id_seq    SEQUENCE OWNED BY     I   ALTER SEQUENCE public.admin_admin_id_seq OWNED BY public.admin.admin_id;
          public               postgres    false    233            �            1259    16647    authentication    TABLE     �   CREATE TABLE public.authentication (
    auth_id integer NOT NULL,
    email text NOT NULL,
    password text NOT NULL,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
 "   DROP TABLE public.authentication;
       public         heap r       postgres    false            �            1259    16646    authentication_auth_id_seq    SEQUENCE     �   CREATE SEQUENCE public.authentication_auth_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 1   DROP SEQUENCE public.authentication_auth_id_seq;
       public               postgres    false    232            �           0    0    authentication_auth_id_seq    SEQUENCE OWNED BY     Y   ALTER SEQUENCE public.authentication_auth_id_seq OWNED BY public.authentication.auth_id;
          public               postgres    false    231            �            1259    16558    birth    TABLE     p  CREATE TABLE public.birth (
    birth_id integer NOT NULL,
    customer_id integer,
    registry_id integer,
    childinfo text,
    birthdate date,
    birthplace text,
    fathersname text,
    mothersname text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updatedat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    sex text
);
    DROP TABLE public.birth;
       public         heap r       postgres    false            �            1259    16557    birth_birth_id_seq    SEQUENCE     �   CREATE SEQUENCE public.birth_birth_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 )   DROP SEQUENCE public.birth_birth_id_seq;
       public               postgres    false    222            �           0    0    birth_birth_id_seq    SEQUENCE OWNED BY     I   ALTER SEQUENCE public.birth_birth_id_seq OWNED BY public.birth.birth_id;
          public               postgres    false    221            �            1259    16539    customer    TABLE     s  CREATE TABLE public.customer (
    customer_id integer NOT NULL,
    fullname text NOT NULL,
    contactno text,
    address text,
    relationship text,
    purpose text,
    civilstatus text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    certificate_type public.cert_type,
    email_address text,
    copies bigint,
    "updatedAt" date[]
);
    DROP TABLE public.customer;
       public         heap r       postgres    false    897            �            1259    16538    customer_customer_id_seq    SEQUENCE     �   CREATE SEQUENCE public.customer_customer_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 /   DROP SEQUENCE public.customer_customer_id_seq;
       public               postgres    false    218            �           0    0    customer_customer_id_seq    SEQUENCE OWNED BY     U   ALTER SEQUENCE public.customer_customer_id_seq OWNED BY public.customer.customer_id;
          public               postgres    false    217            �            1259    16600    death    TABLE     3  CREATE TABLE public.death (
    death_id integer NOT NULL,
    customer_id integer,
    deceasedname text,
    deathdate date,
    birthdate date,
    age integer,
    deathplace text,
    civilstatus text,
    religion text,
    citizenship text,
    residence text,
    occupation text,
    fathersname text,
    mothersname text,
    corpsedisposal text,
    cemeteryaddress text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updatedat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    sex text,
    registry_id integer
);
    DROP TABLE public.death;
       public         heap r       postgres    false            �            1259    16599    death_death_id_seq    SEQUENCE     �   CREATE SEQUENCE public.death_death_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 )   DROP SEQUENCE public.death_death_id_seq;
       public               postgres    false    226            �           0    0    death_death_id_seq    SEQUENCE OWNED BY     I   ALTER SEQUENCE public.death_death_id_seq OWNED BY public.death.death_id;
          public               postgres    false    225            �            1259    16579    marriage    TABLE     W  CREATE TABLE public.marriage (
    marriage_id integer NOT NULL,
    customer_id integer,
    registry_id integer,
    husbandname text,
    wifename text,
    marriagedate date,
    marriageplace text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updatedat timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
    DROP TABLE public.marriage;
       public         heap r       postgres    false            �            1259    16578    marriage_marriage_id_seq    SEQUENCE     �   CREATE SEQUENCE public.marriage_marriage_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 /   DROP SEQUENCE public.marriage_marriage_id_seq;
       public               postgres    false    224            �           0    0    marriage_marriage_id_seq    SEQUENCE OWNED BY     U   ALTER SEQUENCE public.marriage_marriage_id_seq OWNED BY public.marriage.marriage_id;
          public               postgres    false    223            �            1259    16616    payment    TABLE     �   CREATE TABLE public.payment (
    payment_id integer NOT NULL,
    customer_id integer,
    amount numeric(10,2),
    payment_mode public.payment_mode_enum,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
    DROP TABLE public.payment;
       public         heap r       postgres    false    906            �            1259    16615    payment_payment_id_seq    SEQUENCE     �   CREATE SEQUENCE public.payment_payment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.payment_payment_id_seq;
       public               postgres    false    228            �           0    0    payment_payment_id_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.payment_payment_id_seq OWNED BY public.payment.payment_id;
          public               postgres    false    227            �            1259    16549    registry    TABLE     z   CREATE TABLE public.registry (
    registry_id integer NOT NULL,
    registryno text,
    bookno text,
    pageno text
);
    DROP TABLE public.registry;
       public         heap r       postgres    false            �            1259    16548    registry_registry_id_seq    SEQUENCE     �   CREATE SEQUENCE public.registry_registry_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 /   DROP SEQUENCE public.registry_registry_id_seq;
       public               postgres    false    220            �           0    0    registry_registry_id_seq    SEQUENCE OWNED BY     U   ALTER SEQUENCE public.registry_registry_id_seq OWNED BY public.registry.registry_id;
          public               postgres    false    219            �            1259    16631    reports    TABLE     �   CREATE TABLE public.reports (
    reports_id integer NOT NULL,
    payment_id integer,
    reportsname text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updatedat timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
    DROP TABLE public.reports;
       public         heap r       postgres    false            �            1259    16630    reports_reports_id_seq    SEQUENCE     �   CREATE SEQUENCE public.reports_reports_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.reports_reports_id_seq;
       public               postgres    false    230            �           0    0    reports_reports_id_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.reports_reports_id_seq OWNED BY public.reports.reports_id;
          public               postgres    false    229            �            1259    24998    transaction    TABLE     �  CREATE TABLE public.transaction (
    transaction_id integer NOT NULL,
    customer_id integer NOT NULL,
    payment_id integer NOT NULL,
    transaction_no text NOT NULL,
    status public.transaction_status DEFAULT 'pending'::public.transaction_status,
    remarks text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    transactiontype text
);
    DROP TABLE public.transaction;
       public         heap r       postgres    false    900    900            �            1259    24997    transaction_transaction_id_seq    SEQUENCE     �   CREATE SEQUENCE public.transaction_transaction_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 5   DROP SEQUENCE public.transaction_transaction_id_seq;
       public               postgres    false    238            �           0    0    transaction_transaction_id_seq    SEQUENCE OWNED BY     a   ALTER SEQUENCE public.transaction_transaction_id_seq OWNED BY public.transaction.transaction_id;
          public               postgres    false    237            �            1259    16674    verifier    TABLE       CREATE TABLE public.verifier (
    verifier_id integer NOT NULL,
    admin_id integer,
    approvedby text,
    verifiedby text,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updatedat timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
    DROP TABLE public.verifier;
       public         heap r       postgres    false            �            1259    16673    verifier_verifier_id_seq    SEQUENCE     �   CREATE SEQUENCE public.verifier_verifier_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 /   DROP SEQUENCE public.verifier_verifier_id_seq;
       public               postgres    false    236            �           0    0    verifier_verifier_id_seq    SEQUENCE OWNED BY     U   ALTER SEQUENCE public.verifier_verifier_id_seq OWNED BY public.verifier.verifier_id;
          public               postgres    false    235            �           2604    16773    admin admin_id    DEFAULT     p   ALTER TABLE ONLY public.admin ALTER COLUMN admin_id SET DEFAULT nextval('public.admin_admin_id_seq'::regclass);
 =   ALTER TABLE public.admin ALTER COLUMN admin_id DROP DEFAULT;
       public               postgres    false    234    233    234            �           2604    16774    authentication auth_id    DEFAULT     �   ALTER TABLE ONLY public.authentication ALTER COLUMN auth_id SET DEFAULT nextval('public.authentication_auth_id_seq'::regclass);
 E   ALTER TABLE public.authentication ALTER COLUMN auth_id DROP DEFAULT;
       public               postgres    false    231    232    232            �           2604    16775    birth birth_id    DEFAULT     p   ALTER TABLE ONLY public.birth ALTER COLUMN birth_id SET DEFAULT nextval('public.birth_birth_id_seq'::regclass);
 =   ALTER TABLE public.birth ALTER COLUMN birth_id DROP DEFAULT;
       public               postgres    false    222    221    222            �           2604    16776    customer customer_id    DEFAULT     |   ALTER TABLE ONLY public.customer ALTER COLUMN customer_id SET DEFAULT nextval('public.customer_customer_id_seq'::regclass);
 C   ALTER TABLE public.customer ALTER COLUMN customer_id DROP DEFAULT;
       public               postgres    false    217    218    218            �           2604    16777    death death_id    DEFAULT     p   ALTER TABLE ONLY public.death ALTER COLUMN death_id SET DEFAULT nextval('public.death_death_id_seq'::regclass);
 =   ALTER TABLE public.death ALTER COLUMN death_id DROP DEFAULT;
       public               postgres    false    226    225    226            �           2604    16778    marriage marriage_id    DEFAULT     |   ALTER TABLE ONLY public.marriage ALTER COLUMN marriage_id SET DEFAULT nextval('public.marriage_marriage_id_seq'::regclass);
 C   ALTER TABLE public.marriage ALTER COLUMN marriage_id DROP DEFAULT;
       public               postgres    false    224    223    224            �           2604    16779    payment payment_id    DEFAULT     x   ALTER TABLE ONLY public.payment ALTER COLUMN payment_id SET DEFAULT nextval('public.payment_payment_id_seq'::regclass);
 A   ALTER TABLE public.payment ALTER COLUMN payment_id DROP DEFAULT;
       public               postgres    false    228    227    228            �           2604    16780    registry registry_id    DEFAULT     |   ALTER TABLE ONLY public.registry ALTER COLUMN registry_id SET DEFAULT nextval('public.registry_registry_id_seq'::regclass);
 C   ALTER TABLE public.registry ALTER COLUMN registry_id DROP DEFAULT;
       public               postgres    false    220    219    220            �           2604    16781    reports reports_id    DEFAULT     x   ALTER TABLE ONLY public.reports ALTER COLUMN reports_id SET DEFAULT nextval('public.reports_reports_id_seq'::regclass);
 A   ALTER TABLE public.reports ALTER COLUMN reports_id DROP DEFAULT;
       public               postgres    false    229    230    230            �           2604    25001    transaction transaction_id    DEFAULT     �   ALTER TABLE ONLY public.transaction ALTER COLUMN transaction_id SET DEFAULT nextval('public.transaction_transaction_id_seq'::regclass);
 I   ALTER TABLE public.transaction ALTER COLUMN transaction_id DROP DEFAULT;
       public               postgres    false    237    238    238            �           2604    16782    verifier verifier_id    DEFAULT     |   ALTER TABLE ONLY public.verifier ALTER COLUMN verifier_id SET DEFAULT nextval('public.verifier_verifier_id_seq'::regclass);
 C   ALTER TABLE public.verifier ALTER COLUMN verifier_id DROP DEFAULT;
       public               postgres    false    235    236    236            �          0    16659    admin 
   TABLE DATA           M   COPY public.admin (admin_id, auth_id, fullname, createdat, role) FROM stdin;
    public               postgres    false    234   �z       �          0    16647    authentication 
   TABLE DATA           M   COPY public.authentication (auth_id, email, password, createdat) FROM stdin;
    public               postgres    false    232   �z       �          0    16558    birth 
   TABLE DATA           �   COPY public.birth (birth_id, customer_id, registry_id, childinfo, birthdate, birthplace, fathersname, mothersname, createdat, updatedat, sex) FROM stdin;
    public               postgres    false    222   �z       �          0    16539    customer 
   TABLE DATA           �   COPY public.customer (customer_id, fullname, contactno, address, relationship, purpose, civilstatus, createdat, certificate_type, email_address, copies, "updatedAt") FROM stdin;
    public               postgres    false    218   {       �          0    16600    death 
   TABLE DATA           	  COPY public.death (death_id, customer_id, deceasedname, deathdate, birthdate, age, deathplace, civilstatus, religion, citizenship, residence, occupation, fathersname, mothersname, corpsedisposal, cemeteryaddress, createdat, updatedat, sex, registry_id) FROM stdin;
    public               postgres    false    226   �{       �          0    16579    marriage 
   TABLE DATA           �   COPY public.marriage (marriage_id, customer_id, registry_id, husbandname, wifename, marriagedate, marriageplace, createdat, updatedat) FROM stdin;
    public               postgres    false    224   �{       �          0    16616    payment 
   TABLE DATA           [   COPY public.payment (payment_id, customer_id, amount, payment_mode, createdat) FROM stdin;
    public               postgres    false    228   "|       �          0    16549    registry 
   TABLE DATA           K   COPY public.registry (registry_id, registryno, bookno, pageno) FROM stdin;
    public               postgres    false    220   ?|       �          0    16631    reports 
   TABLE DATA           \   COPY public.reports (reports_id, payment_id, reportsname, createdat, updatedat) FROM stdin;
    public               postgres    false    230   d|       �          0    24998    transaction 
   TABLE DATA           �   COPY public.transaction (transaction_id, customer_id, payment_id, transaction_no, status, remarks, created_at, updated_at, transactiontype) FROM stdin;
    public               postgres    false    238   �|       �          0    16674    verifier 
   TABLE DATA           g   COPY public.verifier (verifier_id, admin_id, approvedby, verifiedby, createdat, updatedat) FROM stdin;
    public               postgres    false    236   �|       �           0    0    admin_admin_id_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('public.admin_admin_id_seq', 1, false);
          public               postgres    false    233            �           0    0    authentication_auth_id_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.authentication_auth_id_seq', 1, false);
          public               postgres    false    231            �           0    0    birth_birth_id_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('public.birth_birth_id_seq', 12, true);
          public               postgres    false    221            �           0    0    customer_customer_id_seq    SEQUENCE SET     G   SELECT pg_catalog.setval('public.customer_customer_id_seq', 55, true);
          public               postgres    false    217            �           0    0    death_death_id_seq    SEQUENCE SET     @   SELECT pg_catalog.setval('public.death_death_id_seq', 1, true);
          public               postgres    false    225            �           0    0    marriage_marriage_id_seq    SEQUENCE SET     G   SELECT pg_catalog.setval('public.marriage_marriage_id_seq', 12, true);
          public               postgres    false    223            �           0    0    payment_payment_id_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.payment_payment_id_seq', 1, false);
          public               postgres    false    227            �           0    0    registry_registry_id_seq    SEQUENCE SET     F   SELECT pg_catalog.setval('public.registry_registry_id_seq', 1, true);
          public               postgres    false    219            �           0    0    reports_reports_id_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.reports_reports_id_seq', 1, false);
          public               postgres    false    229            �           0    0    transaction_transaction_id_seq    SEQUENCE SET     M   SELECT pg_catalog.setval('public.transaction_transaction_id_seq', 1, false);
          public               postgres    false    237            �           0    0    verifier_verifier_id_seq    SEQUENCE SET     G   SELECT pg_catalog.setval('public.verifier_verifier_id_seq', 1, false);
          public               postgres    false    235            �           2606    16667    admin admin_pkey 
   CONSTRAINT     T   ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_pkey PRIMARY KEY (admin_id);
 :   ALTER TABLE ONLY public.admin DROP CONSTRAINT admin_pkey;
       public                 postgres    false    234            �           2606    16657 '   authentication authentication_email_key 
   CONSTRAINT     c   ALTER TABLE ONLY public.authentication
    ADD CONSTRAINT authentication_email_key UNIQUE (email);
 Q   ALTER TABLE ONLY public.authentication DROP CONSTRAINT authentication_email_key;
       public                 postgres    false    232            �           2606    16655 "   authentication authentication_pkey 
   CONSTRAINT     e   ALTER TABLE ONLY public.authentication
    ADD CONSTRAINT authentication_pkey PRIMARY KEY (auth_id);
 L   ALTER TABLE ONLY public.authentication DROP CONSTRAINT authentication_pkey;
       public                 postgres    false    232            �           2606    16567    birth birth_pkey 
   CONSTRAINT     T   ALTER TABLE ONLY public.birth
    ADD CONSTRAINT birth_pkey PRIMARY KEY (birth_id);
 :   ALTER TABLE ONLY public.birth DROP CONSTRAINT birth_pkey;
       public                 postgres    false    222            �           2606    16547    customer customer_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (customer_id);
 @   ALTER TABLE ONLY public.customer DROP CONSTRAINT customer_pkey;
       public                 postgres    false    218            �           2606    16609    death death_pkey 
   CONSTRAINT     T   ALTER TABLE ONLY public.death
    ADD CONSTRAINT death_pkey PRIMARY KEY (death_id);
 :   ALTER TABLE ONLY public.death DROP CONSTRAINT death_pkey;
       public                 postgres    false    226            �           2606    16588    marriage marriage_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.marriage
    ADD CONSTRAINT marriage_pkey PRIMARY KEY (marriage_id);
 @   ALTER TABLE ONLY public.marriage DROP CONSTRAINT marriage_pkey;
       public                 postgres    false    224            �           2606    16624    payment payment_pkey 
   CONSTRAINT     Z   ALTER TABLE ONLY public.payment
    ADD CONSTRAINT payment_pkey PRIMARY KEY (payment_id);
 >   ALTER TABLE ONLY public.payment DROP CONSTRAINT payment_pkey;
       public                 postgres    false    228            �           2606    16556    registry registry_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.registry
    ADD CONSTRAINT registry_pkey PRIMARY KEY (registry_id);
 @   ALTER TABLE ONLY public.registry DROP CONSTRAINT registry_pkey;
       public                 postgres    false    220            �           2606    16640    reports reports_pkey 
   CONSTRAINT     Z   ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_pkey PRIMARY KEY (reports_id);
 >   ALTER TABLE ONLY public.reports DROP CONSTRAINT reports_pkey;
       public                 postgres    false    230            �           2606    25008    transaction transaction_pkey 
   CONSTRAINT     f   ALTER TABLE ONLY public.transaction
    ADD CONSTRAINT transaction_pkey PRIMARY KEY (transaction_id);
 F   ALTER TABLE ONLY public.transaction DROP CONSTRAINT transaction_pkey;
       public                 postgres    false    238            �           2606    25010 *   transaction transaction_transaction_no_key 
   CONSTRAINT     o   ALTER TABLE ONLY public.transaction
    ADD CONSTRAINT transaction_transaction_no_key UNIQUE (transaction_no);
 T   ALTER TABLE ONLY public.transaction DROP CONSTRAINT transaction_transaction_no_key;
       public                 postgres    false    238            �           2606    16683    verifier verifier_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.verifier
    ADD CONSTRAINT verifier_pkey PRIMARY KEY (verifier_id);
 @   ALTER TABLE ONLY public.verifier DROP CONSTRAINT verifier_pkey;
       public                 postgres    false    236            �           2606    16668    admin admin_auth_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_auth_id_fkey FOREIGN KEY (auth_id) REFERENCES public.authentication(auth_id);
 B   ALTER TABLE ONLY public.admin DROP CONSTRAINT admin_auth_id_fkey;
       public               postgres    false    234    232    4846            �           2606    16568    birth birth_customer_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.birth
    ADD CONSTRAINT birth_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES public.customer(customer_id);
 F   ALTER TABLE ONLY public.birth DROP CONSTRAINT birth_customer_id_fkey;
       public               postgres    false    4830    222    218            �           2606    16573    birth birth_registry_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.birth
    ADD CONSTRAINT birth_registry_id_fkey FOREIGN KEY (registry_id) REFERENCES public.registry(registry_id);
 F   ALTER TABLE ONLY public.birth DROP CONSTRAINT birth_registry_id_fkey;
       public               postgres    false    4832    220    222            �           2606    16610    death death_customer_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.death
    ADD CONSTRAINT death_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES public.customer(customer_id);
 F   ALTER TABLE ONLY public.death DROP CONSTRAINT death_customer_id_fkey;
       public               postgres    false    218    4830    226                        2606    25011    transaction fk_customer    FK CONSTRAINT     �   ALTER TABLE ONLY public.transaction
    ADD CONSTRAINT fk_customer FOREIGN KEY (customer_id) REFERENCES public.customer(customer_id) ON DELETE CASCADE;
 A   ALTER TABLE ONLY public.transaction DROP CONSTRAINT fk_customer;
       public               postgres    false    4830    238    218                       2606    25016    transaction fk_payment    FK CONSTRAINT     �   ALTER TABLE ONLY public.transaction
    ADD CONSTRAINT fk_payment FOREIGN KEY (payment_id) REFERENCES public.payment(payment_id) ON DELETE CASCADE;
 @   ALTER TABLE ONLY public.transaction DROP CONSTRAINT fk_payment;
       public               postgres    false    238    228    4840            �           2606    16589 "   marriage marriage_customer_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.marriage
    ADD CONSTRAINT marriage_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES public.customer(customer_id);
 L   ALTER TABLE ONLY public.marriage DROP CONSTRAINT marriage_customer_id_fkey;
       public               postgres    false    218    224    4830            �           2606    16594 "   marriage marriage_registry_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.marriage
    ADD CONSTRAINT marriage_registry_id_fkey FOREIGN KEY (registry_id) REFERENCES public.registry(registry_id);
 L   ALTER TABLE ONLY public.marriage DROP CONSTRAINT marriage_registry_id_fkey;
       public               postgres    false    220    4832    224            �           2606    16625     payment payment_customer_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.payment
    ADD CONSTRAINT payment_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES public.customer(customer_id);
 J   ALTER TABLE ONLY public.payment DROP CONSTRAINT payment_customer_id_fkey;
       public               postgres    false    228    218    4830            �           2606    16641    reports reports_payment_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES public.payment(payment_id);
 I   ALTER TABLE ONLY public.reports DROP CONSTRAINT reports_payment_id_fkey;
       public               postgres    false    228    230    4840            �           2606    16684    verifier verifier_admin_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.verifier
    ADD CONSTRAINT verifier_admin_id_fkey FOREIGN KEY (admin_id) REFERENCES public.admin(admin_id);
 I   ALTER TABLE ONLY public.verifier DROP CONSTRAINT verifier_admin_id_fkey;
       public               postgres    false    234    4848    236            �      x������ � �      �      x������ � �      �      x������ � �      �   �   x�-�=�  ��q
.P	��1����v��I�I�z~����H�C�O�?�淝(Ԩ4����.�+��Uk�m{l�����J4���<p���P��F��}k����m�DIeO!���R3h�l��/�)�      �      x������ � �      �   H   x�}���0�j�����"���3�	�H�(c�y�	�BEY��ΔWӣ�97خ�����>�L      �      x������ � �      �      x�3�4��"�=... ��      �      x������ � �      �      x������ � �      �      x������ � �     