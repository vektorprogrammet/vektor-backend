<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210312110941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_rule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, resource VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL, is_routing_rule BOOLEAN NOT NULL, for_executive_board BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE access_rule_user (access_rule_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(access_rule_id, user_id))');
        $this->addSql('CREATE INDEX IDX_8FE49CC5BC6BD944 ON access_rule_user (access_rule_id)');
        $this->addSql('CREATE INDEX IDX_8FE49CC5A76ED395 ON access_rule_user (user_id)');
        $this->addSql('CREATE TABLE access_rule_team (access_rule_id INTEGER NOT NULL, team_id INTEGER NOT NULL, PRIMARY KEY(access_rule_id, team_id))');
        $this->addSql('CREATE INDEX IDX_C697EC93BC6BD944 ON access_rule_team (access_rule_id)');
        $this->addSql('CREATE INDEX IDX_C697EC93296CD8AE ON access_rule_team (team_id)');
        $this->addSql('CREATE TABLE access_rule_role (access_rule_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(access_rule_id, role_id))');
        $this->addSql('CREATE INDEX IDX_551EC0E6BC6BD944 ON access_rule_role (access_rule_id)');
        $this->addSql('CREATE INDEX IDX_551EC0E6D60322AC ON access_rule_role (role_id)');
        $this->addSql('CREATE TABLE admission_notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subscriber_id INTEGER DEFAULT NULL, semester_id INTEGER DEFAULT NULL, department_id INTEGER DEFAULT NULL, timestamp DATETIME NOT NULL, info_meeting BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_EBEBA8557808B1AD ON admission_notification (subscriber_id)');
        $this->addSql('CREATE INDEX IDX_EBEBA8554A798B6F ON admission_notification (semester_id)');
        $this->addSql('CREATE INDEX IDX_EBEBA855AE80F5DF ON admission_notification (department_id)');
        $this->addSql('CREATE TABLE admission_period (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, department_id INTEGER DEFAULT NULL, info_meeting_id INTEGER DEFAULT NULL, semester_id INTEGER DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_FF2BCA84AE80F5DF ON admission_period (department_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF2BCA843F860130 ON admission_period (info_meeting_id)');
        $this->addSql('CREATE INDEX IDX_FF2BCA844A798B6F ON admission_period (semester_id)');
        $this->addSql('CREATE TABLE admission_subscriber (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, department_id INTEGER DEFAULT NULL, email VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, unsubscribe_code VARCHAR(255) NOT NULL, from_application BOOLEAN DEFAULT \'0\' NOT NULL, info_meeting BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('CREATE INDEX IDX_4F497EB7AE80F5DF ON admission_subscriber (department_id)');
        $this->addSql('CREATE TABLE application (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, admission_period_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, interview_id INTEGER DEFAULT NULL, year_of_study VARCHAR(20) NOT NULL, monday BOOLEAN DEFAULT \'1\' NOT NULL, tuesday BOOLEAN DEFAULT \'1\' NOT NULL, wednesday BOOLEAN DEFAULT \'1\' NOT NULL, thursday BOOLEAN DEFAULT \'1\' NOT NULL, friday BOOLEAN DEFAULT \'1\' NOT NULL, substitute BOOLEAN DEFAULT \'0\' NOT NULL, language VARCHAR(255) DEFAULT NULL, double_position BOOLEAN DEFAULT \'0\' NOT NULL, preferred_group VARCHAR(255) DEFAULT NULL, preferred_school VARCHAR(255) DEFAULT NULL, previous_participation BOOLEAN NOT NULL, last_edited DATETIME NOT NULL, created DATETIME NOT NULL, heard_about_from CLOB NOT NULL --(DC2Type:array)
        , team_interest BOOLEAN NOT NULL, special_needs VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_A45BDDC11DD4B017 ON application (admission_period_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1A76ED395 ON application (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A45BDDC155D69D95 ON application (interview_id)');
        $this->addSql('CREATE TABLE application_team (application_id INTEGER NOT NULL, team_id INTEGER NOT NULL, PRIMARY KEY(application_id, team_id))');
        $this->addSql('CREATE INDEX IDX_330CCE973E030ACD ON application_team (application_id)');
        $this->addSql('CREATE INDEX IDX_330CCE97296CD8AE ON application_team (team_id)');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, article CLOB NOT NULL, image_large VARCHAR(255) NOT NULL, image_small VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, sticky BOOLEAN NOT NULL, published BOOLEAN DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66989D9B62 ON article (slug)');
        $this->addSql('CREATE INDEX IDX_23A0E66F675F31B ON article (author_id)');
        $this->addSql('CREATE TABLE articles_departments (article_id INTEGER NOT NULL, department_id INTEGER NOT NULL, PRIMARY KEY(article_id, department_id))');
        $this->addSql('CREATE INDEX IDX_B29B8FB57294869C ON articles_departments (article_id)');
        $this->addSql('CREATE INDEX IDX_B29B8FB5AE80F5DF ON articles_departments (department_id)');
        $this->addSql('CREATE TABLE assistant_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, semester_id INTEGER DEFAULT NULL, department_id INTEGER DEFAULT NULL, school_id INTEGER DEFAULT NULL, workdays VARCHAR(255) NOT NULL, bolk VARCHAR(255) DEFAULT NULL, day VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_1B31A1DBA76ED395 ON assistant_history (user_id)');
        $this->addSql('CREATE INDEX IDX_1B31A1DB4A798B6F ON assistant_history (semester_id)');
        $this->addSql('CREATE INDEX IDX_1B31A1DBAE80F5DF ON assistant_history (department_id)');
        $this->addSql('CREATE INDEX IDX_1B31A1DBC32A47EE ON assistant_history (school_id)');
        $this->addSql('CREATE TABLE certificate_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_6E343C40A76ED395 ON certificate_request (user_id)');
        $this->addSql('CREATE TABLE change_log_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(40) NOT NULL, description VARCHAR(1000) DEFAULT NULL, githubLink VARCHAR(1000) NOT NULL, date DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE department (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(250) NOT NULL, short_name VARCHAR(50) NOT NULL, email VARCHAR(250) NOT NULL, address VARCHAR(250) DEFAULT NULL, city VARCHAR(250) NOT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, slack_channel VARCHAR(255) DEFAULT NULL, logo_path VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT \'1\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD1DE18A2D5B0234 ON department (city)');
        $this->addSql('CREATE TABLE department_school (department_id INTEGER NOT NULL, school_id INTEGER NOT NULL, PRIMARY KEY(department_id, school_id))');
        $this->addSql('CREATE INDEX IDX_ED84254BAE80F5DF ON department_school (department_id)');
        $this->addSql('CREATE INDEX IDX_ED84254BC32A47EE ON department_school (school_id)');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, department_id INTEGER DEFAULT NULL, semester_id INTEGER DEFAULT NULL, role_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, link VARCHAR(250) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7AE80F5DF ON event (department_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA74A798B6F ON event (semester_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7D60322AC ON event (role_id)');
        $this->addSql('CREATE TABLE executive_board (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(250) NOT NULL, email VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, short_description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE executive_board_membership (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, board_id INTEGER DEFAULT NULL, start_semester_id INTEGER DEFAULT NULL, end_semester_id INTEGER DEFAULT NULL, position_name CLOB DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_F6490587A76ED395 ON executive_board_membership (user_id)');
        $this->addSql('CREATE INDEX IDX_F6490587E7EC5785 ON executive_board_membership (board_id)');
        $this->addSql('CREATE INDEX IDX_F6490587100E9BDB ON executive_board_membership (start_semester_id)');
        $this->addSql('CREATE INDEX IDX_F649058740776D10 ON executive_board_membership (end_semester_id)');
        $this->addSql('CREATE TABLE feedback (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, title VARCHAR(55) NOT NULL, description VARCHAR(500) NOT NULL, type VARCHAR(45) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D2294458A76ED395 ON feedback (user_id)');
        $this->addSql('CREATE TABLE field_of_study (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, department_id INTEGER DEFAULT NULL, name VARCHAR(250) NOT NULL, short_name VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8F32491AAE80F5DF ON field_of_study (department_id)');
        $this->addSql('CREATE TABLE infomeeting (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, show_on_page BOOLEAN DEFAULT NULL, date DATETIME DEFAULT NULL, room VARCHAR(250) DEFAULT NULL, description VARCHAR(250) DEFAULT NULL, link VARCHAR(250) DEFAULT NULL)');
        $this->addSql('CREATE TABLE interview (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, schema_id INTEGER DEFAULT NULL, interviewer_id INTEGER DEFAULT NULL, co_interviewer_id INTEGER DEFAULT NULL, interview_score_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, interviewed BOOLEAN NOT NULL, scheduled DATETIME DEFAULT NULL, last_schedule_changed DATETIME DEFAULT NULL, room VARCHAR(255) DEFAULT NULL, campus VARCHAR(255) DEFAULT NULL, map_link VARCHAR(500) DEFAULT NULL, conducted DATETIME DEFAULT NULL, interview_status INTEGER NOT NULL, response_code VARCHAR(255) DEFAULT NULL, cancel_message VARCHAR(2000) DEFAULT NULL, new_time_message VARCHAR(2000) NOT NULL, num_accept_interview_reminders_sent INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('CREATE INDEX IDX_CF1D3C34EA1BEF35 ON interview (schema_id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C347906D9E8 ON interview (interviewer_id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C342C97900A ON interview (co_interviewer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF1D3C347B23C9B2 ON interview (interview_score_id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C34A76ED395 ON interview (user_id)');
        $this->addSql('CREATE TABLE interview_answer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, interview_id INTEGER DEFAULT NULL, question_id INTEGER DEFAULT NULL, answer CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('CREATE INDEX IDX_BA24465255D69D95 ON interview_answer (interview_id)');
        $this->addSql('CREATE INDEX IDX_BA2446521E27F6BF ON interview_answer (question_id)');
        $this->addSql('CREATE TABLE interview_question (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question CLOB NOT NULL, help CLOB DEFAULT NULL, type VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE interview_question_alternative (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question_id INTEGER DEFAULT NULL, alternative VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_711360B41E27F6BF ON interview_question_alternative (question_id)');
        $this->addSql('CREATE TABLE interview_schema (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE interview_schemas_questions (schema_id INTEGER NOT NULL, question_id INTEGER NOT NULL, PRIMARY KEY(schema_id, question_id))');
        $this->addSql('CREATE INDEX IDX_F5387490EA1BEF35 ON interview_schemas_questions (schema_id)');
        $this->addSql('CREATE INDEX IDX_F53874901E27F6BF ON interview_schemas_questions (question_id)');
        $this->addSql('CREATE TABLE interview_score (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, explanatory_power INTEGER NOT NULL, role_model INTEGER NOT NULL, suitability INTEGER NOT NULL, suitable_assistant VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE password_reset (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user INTEGER DEFAULT NULL, hashed_reset_code VARCHAR(255) NOT NULL, reset_time DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_B10172528D93D649 ON password_reset (user)');
        $this->addSql('CREATE TABLE position (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE receipt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, submit_date DATETIME DEFAULT NULL, receipt_date DATETIME NOT NULL, refund_date DATETIME DEFAULT NULL, picture_path VARCHAR(255) DEFAULT NULL, description CLOB NOT NULL, sum DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, visual_id VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_5399B645A76ED395 ON receipt (user_id)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(30) NOT NULL, role VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE TABLE school (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, contact_person VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, international BOOLEAN NOT NULL, active BOOLEAN DEFAULT \'1\' NOT NULL)');
        $this->addSql('CREATE TABLE school_capacity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, school_id INTEGER DEFAULT NULL, semester_id INTEGER DEFAULT NULL, department_id INTEGER DEFAULT NULL, monday INTEGER NOT NULL, tuesday INTEGER NOT NULL, wednesday INTEGER NOT NULL, thursday INTEGER NOT NULL, friday INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_4BAE8530C32A47EE ON school_capacity (school_id)');
        $this->addSql('CREATE INDEX IDX_4BAE85304A798B6F ON school_capacity (semester_id)');
        $this->addSql('CREATE INDEX IDX_4BAE8530AE80F5DF ON school_capacity (department_id)');
        $this->addSql('CREATE TABLE semester (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, semester_time VARCHAR(255) NOT NULL, year VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE signature (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, signature_path VARCHAR(45) DEFAULT NULL, description VARCHAR(250) NOT NULL, additional_comment VARCHAR(500) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AE880141A76ED395 ON signature (user_id)');
        $this->addSql('CREATE TABLE sponsor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, logo_image_path VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE static_content (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, html_id VARCHAR(50) NOT NULL, html CLOB NOT NULL)');
        $this->addSql('CREATE TABLE survey (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, semester_id INTEGER DEFAULT NULL, department_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, show_custom_pop_up_message BOOLEAN NOT NULL, finish_page_content CLOB DEFAULT NULL, confidential BOOLEAN DEFAULT \'0\' NOT NULL, target_audience INTEGER DEFAULT 0 NOT NULL, survey_pop_up_message CLOB DEFAULT \'Svar på undersøkelse!\' NOT NULL)');
        $this->addSql('CREATE INDEX IDX_AD5F9BFC4A798B6F ON survey (semester_id)');
        $this->addSql('CREATE INDEX IDX_AD5F9BFCAE80F5DF ON survey (department_id)');
        $this->addSql('CREATE TABLE survey_surveys_questions (survey_id INTEGER NOT NULL, question_id INTEGER NOT NULL, PRIMARY KEY(survey_id, question_id))');
        $this->addSql('CREATE INDEX IDX_DD4B0A34B3FE509D ON survey_surveys_questions (survey_id)');
        $this->addSql('CREATE INDEX IDX_DD4B0A341E27F6BF ON survey_surveys_questions (question_id)');
        $this->addSql('CREATE TABLE survey_answer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question_id INTEGER DEFAULT NULL, survey_taken_id INTEGER DEFAULT NULL, answer CLOB DEFAULT NULL, answer_array CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('CREATE INDEX IDX_F2D382491E27F6BF ON survey_answer (question_id)');
        $this->addSql('CREATE INDEX IDX_F2D382494D16DAC3 ON survey_answer (survey_taken_id)');
        $this->addSql('CREATE TABLE survey_link_click (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, notification_id INTEGER DEFAULT NULL, time_of_visit DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_503794C9EF1A9D84 ON survey_link_click (notification_id)');
        $this->addSql('CREATE TABLE survey_notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, survey_notification_collection_id INTEGER DEFAULT NULL, time_notification_Sent DATETIME DEFAULT NULL, user_identifier VARCHAR(255) NOT NULL, sent BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF867A9AD0494586 ON survey_notification (user_identifier)');
        $this->addSql('CREATE INDEX IDX_CF867A9AA76ED395 ON survey_notification (user_id)');
        $this->addSql('CREATE INDEX IDX_CF867A9AF4EF17D2 ON survey_notification (survey_notification_collection_id)');
        $this->addSql('CREATE TABLE survey_notification_collection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, survey_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, time_of_notification DATETIME NOT NULL, notification_type INTEGER NOT NULL, all_sent BOOLEAN NOT NULL, active BOOLEAN DEFAULT \'0\' NOT NULL, sms_message VARCHAR(255) NOT NULL, email_from_name VARCHAR(255) NOT NULL, email_subject VARCHAR(255) NOT NULL, email_message CLOB NOT NULL, email_end_message CLOB NOT NULL, email_type INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_F31C0595B3FE509D ON survey_notification_collection (survey_id)');
        $this->addSql('CREATE TABLE survey_notification_collection_user_group (survey_notification_collection_id INTEGER NOT NULL, user_group_id INTEGER NOT NULL, PRIMARY KEY(survey_notification_collection_id, user_group_id))');
        $this->addSql('CREATE INDEX IDX_99307B7BF4EF17D2 ON survey_notification_collection_user_group (survey_notification_collection_id)');
        $this->addSql('CREATE INDEX IDX_99307B7B1ED93D47 ON survey_notification_collection_user_group (user_group_id)');
        $this->addSql('CREATE TABLE survey_question (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question VARCHAR(255) NOT NULL, optional BOOLEAN NOT NULL, help VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE survey_question_alternative (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question_id INTEGER DEFAULT NULL, alternative VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D959462E1E27F6BF ON survey_question_alternative (question_id)');
        $this->addSql('CREATE TABLE survey_taken (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, school_id INTEGER DEFAULT NULL, survey_id INTEGER DEFAULT NULL, time DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_B3982430A76ED395 ON survey_taken (user_id)');
        $this->addSql('CREATE INDEX IDX_B3982430C32A47EE ON survey_taken (school_id)');
        $this->addSql('CREATE INDEX IDX_B3982430B3FE509D ON survey_taken (survey_id)');
        $this->addSql('CREATE TABLE team (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, department_id INTEGER DEFAULT NULL, name VARCHAR(250) NOT NULL, email VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, short_description VARCHAR(255) DEFAULT NULL, accept_application BOOLEAN DEFAULT NULL, deadline DATETIME DEFAULT NULL, active BOOLEAN DEFAULT \'1\' NOT NULL)');
        $this->addSql('CREATE INDEX IDX_C4E0A61FAE80F5DF ON team (department_id)');
        $this->addSql('CREATE TABLE team_application (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, team_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, field_of_study VARCHAR(255) NOT NULL, year_of_study VARCHAR(255) NOT NULL, motivation_text CLOB NOT NULL, biography CLOB NOT NULL, phone VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_4F6B328C296CD8AE ON team_application (team_id)');
        $this->addSql('CREATE TABLE team_interest (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, semester_id INTEGER DEFAULT NULL, department_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D47CC6194A798B6F ON team_interest (semester_id)');
        $this->addSql('CREATE INDEX IDX_D47CC619AE80F5DF ON team_interest (department_id)');
        $this->addSql('CREATE TABLE team_interest_team (team_interest_id INTEGER NOT NULL, team_id INTEGER NOT NULL, PRIMARY KEY(team_interest_id, team_id))');
        $this->addSql('CREATE INDEX IDX_1FAC0302B1A510C4 ON team_interest_team (team_interest_id)');
        $this->addSql('CREATE INDEX IDX_1FAC0302296CD8AE ON team_interest_team (team_id)');
        $this->addSql('CREATE TABLE team_membership (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, start_semester_id INTEGER DEFAULT NULL, end_semester_id INTEGER DEFAULT NULL, team_id INTEGER DEFAULT NULL, position_id INTEGER DEFAULT NULL, deleted_team_name VARCHAR(255) DEFAULT NULL, is_team_leader BOOLEAN NOT NULL, is_suspended BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_B826A040A76ED395 ON team_membership (user_id)');
        $this->addSql('CREATE INDEX IDX_B826A040100E9BDB ON team_membership (start_semester_id)');
        $this->addSql('CREATE INDEX IDX_B826A04040776D10 ON team_membership (end_semester_id)');
        $this->addSql('CREATE INDEX IDX_B826A040296CD8AE ON team_membership (team_id)');
        $this->addSql('CREATE INDEX IDX_B826A040DD842E46 ON team_membership (position_id)');
        $this->addSql('CREATE TABLE unhandled_access_rule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, resource VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, field_of_study_id INTEGER DEFAULT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, gender BOOLEAN NOT NULL, picture_path VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, account_number VARCHAR(45) DEFAULT NULL, user_name VARCHAR(255) DEFAULT NULL, password VARCHAR(64) DEFAULT NULL, email VARCHAR(255) NOT NULL, company_email VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, reserved_from_pop_up BOOLEAN NOT NULL, last_pop_up_time DATETIME NOT NULL, new_user_code VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64924A232CF ON user (user_name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A063DE11 ON user (company_email)');
        $this->addSql('CREATE INDEX IDX_8D93D6499E9C46D5 ON user (field_of_study_id)');
        $this->addSql('CREATE TABLE user_role (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
        $this->addSql('CREATE TABLE user_group_collection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, number_of_user_groups INTEGER NOT NULL, assistant_bolk CLOB NOT NULL --(DC2Type:array)
        , deletable BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE user_group_collection_team (user_group_collection_id INTEGER NOT NULL, team_id INTEGER NOT NULL, PRIMARY KEY(user_group_collection_id, team_id))');
        $this->addSql('CREATE INDEX IDX_F0005A562463F7CD ON user_group_collection_team (user_group_collection_id)');
        $this->addSql('CREATE INDEX IDX_F0005A56296CD8AE ON user_group_collection_team (team_id)');
        $this->addSql('CREATE TABLE user_group_collection_semester (user_group_collection_id INTEGER NOT NULL, semester_id INTEGER NOT NULL, PRIMARY KEY(user_group_collection_id, semester_id))');
        $this->addSql('CREATE INDEX IDX_D23014B72463F7CD ON user_group_collection_semester (user_group_collection_id)');
        $this->addSql('CREATE INDEX IDX_D23014B74A798B6F ON user_group_collection_semester (semester_id)');
        $this->addSql('CREATE TABLE user_group_collection_user (user_group_collection_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(user_group_collection_id, user_id))');
        $this->addSql('CREATE INDEX IDX_B9732A002463F7CD ON user_group_collection_user (user_group_collection_id)');
        $this->addSql('CREATE INDEX IDX_B9732A00A76ED395 ON user_group_collection_user (user_id)');
        $this->addSql('CREATE TABLE user_group_collection_department (user_group_collection_id INTEGER NOT NULL, department_id INTEGER NOT NULL, PRIMARY KEY(user_group_collection_id, department_id))');
        $this->addSql('CREATE INDEX IDX_9D930B062463F7CD ON user_group_collection_department (user_group_collection_id)');
        $this->addSql('CREATE INDEX IDX_9D930B06AE80F5DF ON user_group_collection_department (department_id)');
        $this->addSql('CREATE TABLE usergroup (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_group_collection_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_4A6478172463F7CD ON usergroup (user_group_collection_id)');
        $this->addSql('CREATE TABLE user_group_user (user_group_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(user_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_3AE4BD51ED93D47 ON user_group_user (user_group_id)');
        $this->addSql('CREATE INDEX IDX_3AE4BD5A76ED395 ON user_group_user (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE access_rule');
        $this->addSql('DROP TABLE access_rule_user');
        $this->addSql('DROP TABLE access_rule_team');
        $this->addSql('DROP TABLE access_rule_role');
        $this->addSql('DROP TABLE admission_notification');
        $this->addSql('DROP TABLE admission_period');
        $this->addSql('DROP TABLE admission_subscriber');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE application_team');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE articles_departments');
        $this->addSql('DROP TABLE assistant_history');
        $this->addSql('DROP TABLE certificate_request');
        $this->addSql('DROP TABLE change_log_item');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE department_school');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE executive_board');
        $this->addSql('DROP TABLE executive_board_membership');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE field_of_study');
        $this->addSql('DROP TABLE infomeeting');
        $this->addSql('DROP TABLE interview');
        $this->addSql('DROP TABLE interview_answer');
        $this->addSql('DROP TABLE interview_question');
        $this->addSql('DROP TABLE interview_question_alternative');
        $this->addSql('DROP TABLE interview_schema');
        $this->addSql('DROP TABLE interview_schemas_questions');
        $this->addSql('DROP TABLE interview_score');
        $this->addSql('DROP TABLE password_reset');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE school_capacity');
        $this->addSql('DROP TABLE semester');
        $this->addSql('DROP TABLE signature');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE static_content');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE survey_surveys_questions');
        $this->addSql('DROP TABLE survey_answer');
        $this->addSql('DROP TABLE survey_link_click');
        $this->addSql('DROP TABLE survey_notification');
        $this->addSql('DROP TABLE survey_notification_collection');
        $this->addSql('DROP TABLE survey_notification_collection_user_group');
        $this->addSql('DROP TABLE survey_question');
        $this->addSql('DROP TABLE survey_question_alternative');
        $this->addSql('DROP TABLE survey_taken');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_application');
        $this->addSql('DROP TABLE team_interest');
        $this->addSql('DROP TABLE team_interest_team');
        $this->addSql('DROP TABLE team_membership');
        $this->addSql('DROP TABLE unhandled_access_rule');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_group_collection');
        $this->addSql('DROP TABLE user_group_collection_team');
        $this->addSql('DROP TABLE user_group_collection_semester');
        $this->addSql('DROP TABLE user_group_collection_user');
        $this->addSql('DROP TABLE user_group_collection_department');
        $this->addSql('DROP TABLE usergroup');
        $this->addSql('DROP TABLE user_group_user');
    }
}
