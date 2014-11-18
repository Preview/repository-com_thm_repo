-- =============================================
-- File: uninstall.sql
-- Type: UTF-8
-- Name: Technische Hochschule Mittelhessen
-- URL : www.mni.thm.de
-- Desc: Un-Install und CleanUp Script for thm_repo
-- Date: 2013-07-24
-- Last: 2013-07-24
-- Auth: Andrej Sajenko <Andrej.Sajenko@mni.thm.de>
-- =============================================


-- Drop all related tables
DROP TABLE IF EXISTS #__thm_repo_link;
DROP TABLE IF EXISTS #__thm_repo_version;
DROP TABLE IF EXISTS #__thm_repo_file;
DROP TABLE IF EXISTS #__thm_repo_entity;
DROP TABLE IF EXISTS #__thm_repo_folder;

-- Remove component related rows from joomla_tabless
