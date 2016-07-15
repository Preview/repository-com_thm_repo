<?php

/**
 * Interface TreeVisitor
 */
interface TreeVisitor
{
	/**
	 * Will be called when entering a Folder.
	 *
	 * @param THMFolder $folder The Folder we are entering.
	 */
	public function enteringFolder($folder);

	/**
	 * Will be called when leaving a Folder.
	 *
	 * @param THMFolder $folder The Folder we leave.
	 */
	public function leavingFolder($folder);

	/**
	 * Will be called when an entity is found.
	 *
	 * @param THMEntity $entity The entity found.
	 */
	public function visitEntity($entity);

	/**
	 * Called after we visited all folders and entities.
	 *
	 * @return mixed
	 */
	public function done();
}