-- =============================================================================
-- Diagram Name: dp
-- Created on: 17. 1. 2008 14:37:43
-- Diagram Version: 
-- =============================================================================

CREATE TABLE "problem_solution_comment" (
  "id" SERIAL,
  "problem_solution_id" int4,
  "subject" varchar(128),
  "body" text,
  "created" date,
  "created_by" int4
);


CREATE TABLE "problem_solution" (
  "id" int4 NOT NULL,
  "problem_id" int4,
  "name" varchar(128),
  "description" text,
  "created" date,
  "created_by" int4,
  PRIMARY KEY("id")
);


CREATE TABLE "inquiry" (
  "id" SERIAL NOT NULL,
  "title" varchar(255) NOT NULL,
  "description" text NOT NULL,
  "start" date,
  "end" date,
  "created_by" int4 NOT NULL,
  "created" date NOT NULL,
  "problem_id" int4,
  PRIMARY KEY("id")
);


CREATE TABLE "inquiry_user" (
  "id" int4 NOT NULL,
  "inquiry_id" int4,
  "user_id" int4,
  "voted" bool,
  "choice_id" int4,
  PRIMARY KEY("id"),
  CONSTRAINT "Constraint0" UNIQUE("inquiry_id","user_id")
);


CREATE TABLE "inquiry_choice" (
  "id" SERIAL NOT NULL,
  "inquiry_id" int4 NOT NULL,
  "problem_solution_id" int4,
  "title" varchar(128) NOT NULL,
  "description" text,
  PRIMARY KEY("id")
);


CREATE TABLE "problem_dependency" (
  "id" int4 NOT NULL,
  "problem_id" int4,
  "problem_depends_id" int4,
  "strenght" int4,
  PRIMARY KEY("id")
);


CREATE TABLE "problem_area" (
  "id" int4 NOT NULL,
  "name" varchar(64) NOT NULL,
  "description" text NOT NULL,
  "state" varchar,
  "created" date NOT NULL,
  "created_by" int4 NOT NULL,
  "updated" date,
  "updated_by" int4,
  CONSTRAINT "problem_area_pkey" PRIMARY KEY("id")
)
WITHOUT OIDS;


CREATE TABLE "problem" (
  "id" int4 NOT NULL,
  "created_by" int4 NOT NULL,
  "problem_id" int4 NOT NULL,
  "created" date NOT NULL,
  "deleted" bool NOT NULL DEFAULT false,
  CONSTRAINT "problem_pkey" PRIMARY KEY("id")
)
WITHOUT OIDS;


CREATE TABLE "problem_attachement" (
  "id" int4 NOT NULL,
  "problem_id" int4 NOT NULL,
  "created_by" int4 NOT NULL,
  "type" varchar(10),
  "filename" varchar(255) NOT NULL,
  "name" varchar(64) NOT NULL,
  "description" text,
  "created" date NOT NULL,
  CONSTRAINT "problem_attachement_pkey" PRIMARY KEY("id")
)
WITHOUT OIDS;


CREATE TABLE "problem_comment" (
  "id" SERIAL NOT NULL,
  "subject" varchar(128) NOT NULL,
  "body" text NOT NULL,
  "problem_id" int4 NOT NULL,
  "created_by" int4 NOT NULL,
  "created" date NOT NULL,
  PRIMARY KEY("id")
);


CREATE TABLE "user" (
  "id" SERIAL NOT NULL,
  "username" varchar(32) NOT NULL,
  "password" varchar(32) NOT NULL,
  "fullname" varchar(64) NOT NULL,
  CONSTRAINT "user_pkey" PRIMARY KEY("id"),
  CONSTRAINT "user_username_key" UNIQUE("username")
)
WITHOUT OIDS;

COMMENT ON TABLE "user" IS 'User accounts';


ALTER TABLE "problem_solution_comment" ADD CONSTRAINT "Ref_04" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_solution_comment" ADD CONSTRAINT "Ref_05" FOREIGN KEY ("problem_solution_id")
    REFERENCES "problem_solution"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_solution" ADD CONSTRAINT "Ref_02" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_solution" ADD CONSTRAINT "Ref_03" FOREIGN KEY ("problem_id")
    REFERENCES "problem"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry" ADD CONSTRAINT "Ref_06" FOREIGN KEY ("problem_id")
    REFERENCES "problem"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry" ADD CONSTRAINT "Ref_07" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry_user" ADD CONSTRAINT "Ref_10" FOREIGN KEY ("inquiry_id")
    REFERENCES "inquiry"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry_user" ADD CONSTRAINT "Ref_11" FOREIGN KEY ("user_id")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry_user" ADD CONSTRAINT "Ref_12" FOREIGN KEY ("choice_id")
    REFERENCES "inquiry_choice"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry_choice" ADD CONSTRAINT "Ref_08" FOREIGN KEY ("inquiry_id")
    REFERENCES "inquiry"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "inquiry_choice" ADD CONSTRAINT "Ref_09" FOREIGN KEY ("problem_solution_id")
    REFERENCES "problem_solution"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_dependency" ADD CONSTRAINT "Ref_00" FOREIGN KEY ("problem_id")
    REFERENCES "problem"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_dependency" ADD CONSTRAINT "Ref_01" FOREIGN KEY ("problem_depends_id")
    REFERENCES "problem"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_area" ADD CONSTRAINT "updated_by_fkey" FOREIGN KEY ("updated_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_area" ADD CONSTRAINT "created_by_fkey" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem" ADD CONSTRAINT "ref_4_fkey" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem" ADD CONSTRAINT "problem_id_fkey" FOREIGN KEY ("problem_id")
    REFERENCES "problem_area"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_attachement" ADD CONSTRAINT "ref_3_fkey" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_attachement" ADD CONSTRAINT "Ref_-01" FOREIGN KEY ("problem_id")
    REFERENCES "problem"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_comment" ADD CONSTRAINT "Ref_-03" FOREIGN KEY ("created_by")
    REFERENCES "user"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

ALTER TABLE "problem_comment" ADD CONSTRAINT "Ref_-02" FOREIGN KEY ("problem_id")
    REFERENCES "problem"("id")
      MATCH SIMPLE
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE;

