<?php

namespace Eva\EvaLivenews\Events;

use Eva\EvaEngine\Exception;
use SocketIO\Emitter;
use Eva\EvaLivenews\Models\NewsManager;

class LivenewsListener
{
    public function afterCreate($event, $news)
    {
        if (!$news->id) {
            return;
        }
        $config = $news->getDI()->getConfig();
        $newsString = '';
        if ($config->livenews->broadcastEnable) {
            $emitter = new Emitter(array(
                'host' => $config->livenews->socketIoRedis->host,
                'port' => $config->livenews->socketIoRedis->port,
            ));
            $newsString = json_encode($news->dump(
                NewsManager::$simpleDump
            ));
            $emitter->emit('livenews:create', $newsString);
        }

        if ($news->status === 'published' && $config->livenews->realtimeCacheEnable) {
            $newsString = $newsString ?: json_encode($news->dump(
                NewsManager::$simpleDump
            ));
            $redis = $news->getDI()->getFastCache();
            $redis->zAdd('livenews', (int) $news->id, $newsString);
            $size = $redis->zSize('livenews');
            if ($size > $config->livenews->realtimeCacheSize) {
                //remove lowest rank
                $redis->zRemRangeByRank('livenews', 0, 0);
            }
        }
    }

    public function afterUpdate($event, $news)
    {
        if (!$news->id) {
            return;
        }
        $config = $news->getDI()->getConfig();
        $newsString = '';
        if ($config->livenews->broadcastEnable) {
            $emitter = new Emitter(array(
                'host' => $config->livenews->socketIoRedis->host,
                'port' => $config->livenews->socketIoRedis->port,
            ));
            $newsString = json_encode($news->dump(
                NewsManager::$simpleDump
            ));
            $emitter->emit('livenews:update', $newsString);
        }

        if ($news->status === 'published' && $config->livenews->realtimeCacheEnable) {
            $newsString = $newsString ?: json_encode($news->dump(
                NewsManager::$simpleDump
            ));
            $redis = $news->getDI()->getFastCache();
            $redis->zAdd('livenews', (int) $news->id, $newsString);
        }
    }

    public function afterRemove($event, $news)
    {
        if (!$news->id) {
            return;
        }
        $config = $news->getDI()->getConfig();
        $newsString = '';
        if ($config->livenews->broadcastEnable) {
            $emitter = new Emitter(array(
                'host' => $config->livenews->socketIoRedis->host,
                'port' => $config->livenews->socketIoRedis->port,
            ));
            $emitter->emit('livenews:remove', $news->id);
        }

        if ($news->status === 'published' && $config->livenews->realtimeCacheEnable) {
            $redis = $news->getDI()->getFastCache();
            $redis->zAdd('livenews', (int) $news->id, $newsString);
        }
    
    }
}
