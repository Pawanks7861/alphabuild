-- AlphaBuild custom schema derived from the tickets tables for local development.

CREATE TABLE `tblforms` LIKE `tbltickets`;
ALTER TABLE `tblforms`
  CHANGE `ticketid` `formid` int(11) NOT NULL AUTO_INCREMENT,
  CHANGE `ticketkey` `formkey` varchar(32) NOT NULL,
  CHANGE `merged_ticket_id` `merged_form_id` int(11) DEFAULT NULL;

CREATE TABLE `tblforms_status` LIKE `tbltickets_status`;
ALTER TABLE `tblforms_status`
  CHANGE `ticketstatusid` `formstatusid` int(11) NOT NULL AUTO_INCREMENT;

INSERT IGNORE INTO `tblforms_status` (`formstatusid`, `name`, `isdefault`, `statuscolor`, `statusorder`)
SELECT `ticketstatusid`, `name`, `isdefault`, `statuscolor`, `statusorder`
FROM `tbltickets_status`;

CREATE TABLE `tblforms_priorities` LIKE `tbltickets_priorities`;
INSERT IGNORE INTO `tblforms_priorities` (`priorityid`, `name`)
SELECT `priorityid`, `name` FROM `tbltickets_priorities`;

CREATE TABLE `tblforms_predefined_replies` LIKE `tbltickets_predefined_replies`;

CREATE TABLE `tblforms_pipe_log` LIKE `tbltickets_pipe_log`;

CREATE TABLE `tblform_attachments` LIKE `tblticket_attachments`;
ALTER TABLE `tblform_attachments`
  CHANGE `ticketid` `formid` int(11) NOT NULL;

CREATE TABLE `tblform_replies` LIKE `tblticket_replies`;
ALTER TABLE `tblform_replies`
  CHANGE `ticketid` `formid` int(11) NOT NULL;
