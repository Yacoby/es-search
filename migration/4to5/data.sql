--This is the sql that contains the data moving that couldn't be done 
--manually that easily (when moving 10 lines, it is probably easier to do 
--it manually)

--things like mod sources were infact ported by setting up the new database,
--running sync and then altering ids utill they matched the old db

-- -----------------------------------------------------------------------------
-- remove game_mods et al

ALTER TABLE modification ADD COLUMN game int NOT NULL

UPDATE modification
SET game_id = gm.id
FROM modification m,
INNER JOIN game_mods gm ON gm.modification_id = m.id