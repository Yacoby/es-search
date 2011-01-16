<?php
class Search_Listener_Site_Limits extends Doctrine_Record_Listener{

    private function getUpdatedDetails($lastUpdateTime, $current, $limit) {
        assert(is_numeric($lastUpdateTime));
        assert(is_numeric($current));
        assert(is_numeric($limit));

        if ( $limit == 0 ){
            return array(
                'bytes_last_updated' => time(),
                'bytes_used'         => 0,
            );
        }

         //get the number of pages we can dl per second (normally 0.xxx);
        $perSec    = $limit / 60 / 60 / 24;

        //work how many pages we have left has changed since we last did this
        $change    = $perSec * ( time() - $lastUpdateTime );

        //only deal in whole numbers, so floor this to get an int
        $changeF   = floor($change);

        //get the amount left over
        $changeRem = $change - $changeF;

        //increase the pages remining by the int
        $current  -= $changeF;

        //but make sure we don't let it run over the max
        if ($current < 0){ 
            $current = 0;
        }

        //work out how many seconds the amount left over is, and remove it from
        // the time, so we can deal deal with it next time.
        //this ensures that we don't end up losing/gaining pages.
        assert($perSec != 0);
        $lastUpdateTime = time() - ceil(( $changeRem / $perSec));
        return array(
            'bytes_last_updated' => $lastUpdateTime,
            'bytes_used'         => $current,
        );
    }


    public function preHydrate(Doctrine_Event $e){
        $data = $e->data;
        $details = $this->getUpdatedDetails($data['bytes_last_updated'],
                                            $data['bytes_used'],
                                            $data['byte_limit']);
        $data = array_merge($data, $details);
        $e->data = $data;
    }

}