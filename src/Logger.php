<?php declare(strict_types=1);

namespace WrapperMpdf;

use Psr\Log\AbstractLogger;
use Tracy\ILogger;


/**
 * Class Logger
 *
 * @author  geniv
 * @package WrapperMpdf
 */
class Logger extends AbstractLogger
{
    /** @var ILogger */
    private $logger;


    /**
     * Logger constructor.
     *
     * @param ILogger $logger
     */
    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Log.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($message, $level);
    }
}
