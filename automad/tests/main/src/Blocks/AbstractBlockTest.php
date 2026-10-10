<?php

namespace Automad\Blocks;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AbstractBlockTest extends TestCase {
	public static function dataForTestFromAgentLayout() {
		return array(
			'omitted stretched clears saved stretched' => array(
				array('layout' => array('stretched' => true)),
				array(),
				null
			),
			'stretched false clears saved stretched' => array(
				array('layout' => array('stretched' => true)),
				array('stretched' => false),
				null
			),
			'omitted width clears saved width' => array(
				array('layout' => array('width' => '1/3')),
				array(),
				null
			),
			'empty width clears saved width' => array(
				array('layout' => array('width' => '1/3')),
				array('width' => ''),
				null
			),
			'new width replaces the saved layout' => array(
				array('layout' => array('stretched' => true, 'width' => '1/2')),
				array('width' => '1/3'),
				array('width' => '1/3')
			),
			'stretched replaces the saved width' => array(
				array('layout' => array('width' => '1/2')),
				array('stretched' => true),
				array('stretched' => true)
			),
			'stretched and width are both applied' => array(
				array(),
				array('stretched' => true, 'width' => '1/2'),
				array('stretched' => true, 'width' => '1/2')
			),
			'unchanged layout stays the same' => array(
				array('layout' => array('width' => '1/2')),
				array('width' => '1/2'),
				array('width' => '1/2')
			),
			'no layout stays without layout' => array(
				array(),
				array(),
				null
			)
		);
	}

	public function testFromAgentKeepsOtherTunes() {
		$otherTunes = array(
			'spacing' => array('top' => '2rem', 'bottom' => '1rem'),
			'id' => 'test-id',
			'className' => 'test-class'
		);

		$saved = array(
			'id' => 'abc',
			'type' => 'paragraph',
			'data' => array('text' => 'Saved'),
			'tunes' => array('layout' => array('stretched' => true, 'width' => '1/2'), ...$otherTunes)
		);

		$cleared = Paragraph::fromAgent(
			array('id' => 'abc', 'type' => 'paragraph', 'data' => array('text' => 'Saved')),
			$saved
		);

		/** @disregard */
		$this->assertSame($otherTunes, $cleared['tunes']);

		$changed = Paragraph::fromAgent(
			array('id' => 'abc', 'type' => 'paragraph', 'width' => '1/4', 'data' => array('text' => 'Saved')),
			$saved
		);

		/** @disregard */
		$this->assertSame(array('width' => '1/4'), $changed['tunes']['layout']);
		/** @disregard */
		$this->assertSame('test-id', $changed['tunes']['id']);
		/** @disregard */
		$this->assertSame('test-class', $changed['tunes']['className']);
		/** @disregard */
		$this->assertSame($otherTunes['spacing'], $changed['tunes']['spacing']);
	}

	#[DataProvider('dataForTestFromAgentLayout')]
	public function testFromAgentLayout(array $savedTunes, array $layoutInput, ?array $expectedLayout) {
		$saved = array(
			'id' => 'abc',
			'type' => 'paragraph',
			'data' => array('text' => 'Saved'),
			'tunes' => $savedTunes
		);

		$block = Paragraph::fromAgent(
			array('id' => 'abc', 'type' => 'paragraph', 'data' => array('text' => 'Updated'), ...$layoutInput),
			$saved
		);

		/** @disregard */
		$this->assertSame('Updated', $block['data']['text']);

		if ($expectedLayout === null) {
			/** @disregard */
			$this->assertArrayNotHasKey('layout', $block['tunes']);
		} else {
			/** @disregard */
			$this->assertSame($expectedLayout, $block['tunes']['layout']);
		}
	}

	public function testFromAgentNestedChildrenAreRebuilt() {
		$saved = array(
			'id' => 'section',
			'type' => 'layoutSection',
			'data' => array(
				'content' => array(
					'blocks' => array(
						array(
							'id' => 'left',
							'type' => 'paragraph',
							'data' => array('text' => 'Left'),
							'tunes' => array('layout' => array('width' => '1/2'))
						),
						array(
							'id' => 'right',
							'type' => 'paragraph',
							'data' => array('text' => 'Right'),
							'tunes' => array('layout' => array('width' => '1/2'))
						)
					)
				)
			),
			'tunes' => array('layout' => array('stretched' => true))
		);

		$block = LayoutSection::fromAgent(
			array(
				'id' => 'section',
				'type' => 'layoutSection',
				'data' => array(
					'content' => array(
						array('id' => 'left', 'type' => 'paragraph', 'width' => '1/3', 'data' => array('text' => 'Left')),
						array('id' => 'right', 'type' => 'paragraph', 'data' => array('text' => 'Right'))
					)
				)
			),
			$saved
		);

		/** @disregard */
		$this->assertArrayNotHasKey('layout', $block['tunes']);

		$children = $block['data']['content']['blocks'];

		/** @disregard */
		$this->assertSame(array('width' => '1/3'), $children[0]['tunes']['layout']);
		/** @disregard */
		$this->assertArrayNotHasKey('layout', $children[1]['tunes']);
	}

	public function testFromAgentNewBlockWithoutLayoutHasNoLayoutTune() {
		$block = Paragraph::fromAgent(array('type' => 'paragraph', 'data' => array('text' => 'New')));

		/** @disregard */
		$this->assertNotEmpty($block['id']);
		/** @disregard */
		$this->assertArrayNotHasKey('layout', $block['tunes']);
	}
}
