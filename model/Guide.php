<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Property.php";

if (!isset($connection)) {
    establish_db_connection();
}


class Guide
{
    /**
     * Guide constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Attributes of an Guide
     */
    public $id;
    public $category;
    public $questions = [];
    public $question_ids = [];


    /**
     * Get the data for an Guide object from the database
     *
     * @param int $id Id of the Guide
     * @return $this The guide object with its attributes
     */
    public function get_by_id($id)
    {
        try {

            global $connection;

            $this->id = $id;


            $sql = "SELECT fk_category FROM Guide WHERE id = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $sql_result = $stmt->fetch();
            $this->category = new Category($sql_result["fk_category"]);
            $this->questions = $this->fetch_questions_and_answers();
        } catch (Exception $e) {
            throw new PDOException($e->getMessage() . "    guide.get_by_id($id)");
        }

    }

    /**
     * Get the data for corresponding Questions and Answers from the database
     *
     * @return array The answers with its attributes
     */
    public function fetch_questions_and_answers()
    {
        try {

            global $connection;

            $id = $this->id;


            $sql = "SELECT fk_question FROM Guide_Questions WHERE fk_guide = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $question_list = [];
            $question_id_list = [];


            foreach ($stmt->fetchAll() as $current_question) {

                $question_id = $current_question['fk_question'];
                $question = new Question($question_id);
                array_push($question_id_list, $question_id);
                array_push($question_list, $question);

            }
            $this->question_ids = $question_id_list;
            $this->questions = $question_list;

            for ($i = 0; $i < count($question_list); $i++) {
                $question_list[$i]->getAnswers();
            }
            return $question_list;
        } catch (Exception $e) {
            throw new PDOException($e->getMessage() . "    guide.fetch_qna($id)");
        }

    }


    /**
     * @return array
     */
    public function get_question_ids(): array
    {
        return $this->question_ids;
    }


}

class Answer
{
    /**
     * Answer constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Attributes of an Guide
     */
    public $id;
    public $question;
    public $answer_text;
    public $description_text;
    public $properties = [];
    public $values = [];


    /**
     * Get the sql selector for this particular Answer
     *
     * @return string The sql selector for this particular Answers
     */
    public function getCondition()
    {
        $filter = "(";
        $flag = False;
        for ($i = 0; $i < count($this->properties); $i++) {
            if (!$flag) {
                $flag = True;
            } else {
                $filter .= ") AND (";
            }
            $property = new Property($this->properties[$i]);
            if ($property->getId() == "price") {
                $filter .= "float_current_price " . $this->values[$i];
            } else {
                if ($property->getType() == "text") {
                    $filter .= "SELECT str_value " . $this->values[$i] .
                        " FROM Article_Property AP2 WHERE AP2.fk_article = AP.fk_article AND AP2.fk_property = " . $property->getId();
                } else {
                    $filter .= "SELECT str_value" . $this->values[$i] .
                        " FROM Article_Property AP2 WHERE AP2.fk_article = AP.fk_article AND AP2.fk_property = " . $property->getId();
                }
            }
        }
        if (!$flag) return "";
        return $filter . " )";
    }

    /**
     * Get the data for an Answer object from the database
     *
     * @param int $id Id of the Answer
     * @return $this The answer object with its attributes
     */
    public function get_by_id($id)
    {
        try {
            global $connection;

            $sql = "SELECT * from Question_Answers where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $sql_result = $stmt->fetch();

            $this->id = $id;
            $this->question = $sql_result["fk_question"];
            $this->answer_text = $sql_result["str_answer"];
            $this->description_text = $sql_result["str_description"];
            $this->properties = explode(";", $sql_result["str_filter_properties"]);
            $this->values = explode(";", $sql_result["str_filter_values"]);

            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . " Answer.get_by_id($id)");
        }
    }
}

class Question
{
    /**
     * Question constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Attributes of an Guide
     */
    public $id;
    public $question_text;
    public $answers;

    /**
     * Get the data for an Question object from the database
     *
     * @param int $id Id of the Question
     * @return $this The question object with its attributes
     */
    public function get_by_id($id)
    {
        try {
            global $connection;

            $sql = "SELECT * from Question where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $sql_result = $stmt->fetch();

            $this->id = $id;
            $this->question_text = $sql_result["str_question"];
            $this->fetch_answers();
            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . "Question.get_by_id");
        }
    }

    /**
     * Get the data for corresponding Answers from the database
     *
     * @return array The answers with its attributes
     */
    public function fetch_answers(): array
    {
        try {

            global $connection;

            $id = $this->id;

            //ID was supplied

            $sql = "SELECT id FROM Question_Answers WHERE fk_question = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $answer_list = [];

            foreach ($stmt->fetchAll() as $current_answer) {
                $answer = new Answer($current_answer['id']);
                array_push($answer_list, $answer);
            }
            $this->answers = $answer_list;
            return $answer_list;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }

    }

    /**
     * @return mixed
     */
    public function getQuestionText()
    {
        return $this->question_text;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}