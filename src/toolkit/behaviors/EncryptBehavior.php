<?php

    namespace yiitk\behaviors;

    use yii\behaviors\AttributeBehavior;
    use yii\db\BaseActiveRecord;
    use yii\db\Expression;

    /**
     * EncryptBehavior automatically fills the specified attributes with the current date and time.
     *
     * @author Jonatas Sas
     * @since  2.0
     */
    class EncryptBehavior extends AttributeBehavior
    {
        /**
         * @var array
         */
       public array $encryptAttributes = [];

        /**
         * @var callable|Expression The expression that will be used for generating the datetime.
         * This can be either an anonymous function that returns the date/time value,
         * or an [[Expression]] object representing a DB expression (e.g. `new Expression('NOW()')`).
         * If not set, it will use the value of `DateTime::format` to set the attributes.
         */
        public $value;

        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            if (empty($this->encryptAttributes)) {
                $this->attributes = [
                    BaseActiveRecord::EVENT_AFTER_FIND => $this->encryptAttributes,
                    BaseActiveRecord::EVENT_BEFORE_INSERT => $this->encryptAttributes,
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->encryptAttributes,
                ];
            }
        }

        /**
         * @inheritdoc
         */
        protected function getValue($event)
        {

            if ($this->value instanceof Expression) {
                return $this->value;
            } else {
                return (($this->value !== null) ? call_user_func($this->value, $event) : new Expression('NOW()'));
            }
        }

        /**
         * Updates a date/time attribute to the current date/time.
         *
         * ```php
         * $model->touch('lastVisit');
         * ```
         *
         * @param string $attribute the name of the attribute to update.
         */
        public function touch($attribute)
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->owner->updateAttributes(array_fill_keys((array)$attribute, $this->getValue(null)));
        }
    }